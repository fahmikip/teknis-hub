<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use App\Notifications\DocumentCreatedNotification;
use App\Notifications\DocumentUpdatedNotification;
use App\Notifications\DocumentVersionUploadedNotification;
use Illuminate\Support\Facades\DB;
use Throwable;

class DocumentService
{
    public function __construct(
        protected DocumentFileService $fileService
    ) {
    }

    public function fileService(): DocumentFileService
    {
        return $this->fileService;
    }

    /**
     * Buat dokumen baru beserta file privat dan versi awal (v1) dalam satu transaksi.
     */
    public function createDocument(array $validated, User $actor): Document
    {
        $file = $validated['file'];
        $storedPath = $this->fileService->store($file, (int) $validated['year']);

        try {
            $document = DB::transaction(function () use ($validated, $storedPath, $file, $actor) {
                $document = Document::create([
                    'title' => $validated['title'],
                    'document_number' => $validated['document_number'] ?? null,
                    'document_type_id' => $validated['document_type_id'],
                    'category_id' => $validated['category_id'],
                    'stage_id' => $validated['stage_id'] ?? null,
                    'year' => $validated['year'],
                    'document_date' => $validated['document_date'] ?? null,
                    'status' => $validated['status'] ?? 'active',
                    'access_level' => $validated['access_level'] ?? 'internal',
                    'description' => $validated['description'] ?? null,
                    'keywords' => $validated['keywords'] ?? null,
                    'created_by' => $actor->id,
                    'updated_by' => $actor->id,
                ]);

                $this->createInitialVersion($document, $storedPath, $file, $actor);

                $this->log(
                    actor: $actor,
                    action: 'create_document',
                    subject: $document,
                    description: sprintf('User membuat dokumen "%s"', $document->title),
                    metadata: null,
                );

                return $document;
            });
        } catch (Throwable $e) {
            $this->fileService->delete($storedPath);
            throw $e;
        }

        $this->sendDocumentCreatedNotification($document, $actor);

        return $document;
    }

    /**
     * Update metadata dokumen (tanpa mengganti file) dalam satu transaksi.
     */
    public function updateDocument(Document $document, array $validated, User $actor): Document
    {
        $changedFields = [];

        $document = DB::transaction(function () use ($document, $validated, $actor, &$changedFields) {
            $changed = [];
            foreach ($validated as $key => $value) {
                if ($this->valuesDiffer($document->{$key}, $value)) {
                    $changed[] = $key;
                }
            }
            $changedFields = array_values(array_unique($changed));

            $document->update(array_merge($validated, ['updated_by' => $actor->id]));

            $this->log(
                actor: $actor,
                action: 'update_document',
                subject: $document,
                description: sprintf('User memperbarui dokumen "%s"', $document->title),
                metadata: ['changed' => $changedFields],
            );

            return $document;
        });

        $this->sendDocumentUpdatedNotification($document, $actor, $changedFields);

        return $document;
    }

    protected function valuesDiffer(mixed $current, mixed $new): bool
    {
        if ($current instanceof \BackedEnum) {
            $current = $current->value;
        }

        if ($new instanceof \BackedEnum) {
            $new = $new->value;
        }

        if ($current instanceof \DateTimeInterface) {
            $current = $current->format('Y-m-d');
        }

        if ($new instanceof \DateTimeInterface) {
            $new = $new->format('Y-m-d');
        }

        return (string) $current !== (string) $new;
    }

    /**
     * Soft delete (arsip) dokumen. File fisik tidak dihapus sehingga tetap dapat dipulihkan.
     */
    public function archiveDocument(Document $document, User $actor): void
    {
        DB::transaction(function () use ($document, $actor) {
            $document->status = \App\Enums\DocumentStatus::Archived;
            $document->updated_by = $actor->id;
            $document->save();

            $document->delete();

            $this->log(
                actor: $actor,
                action: 'archive_document',
                subject: $document,
                description: sprintf('User mengarsipkan dokumen "%s"', $document->title),
            );
        });
    }

    /**
     * Buat versi awal dokumen (v1).
     */
    protected function createInitialVersion(Document $document, string $path, $file, User $actor): DocumentVersion
    {
        return DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => 1,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'notes' => 'Versi awal dokumen',
            'uploaded_by' => $actor->id,
        ]);
    }

    /**
     * Tambahkan versi baru dokumen dari file PDF yang diunggah.
     */
    public function addVersion(Document $document, array $validated, User $actor): DocumentVersion
    {
        $nextVersion = ($document->versions()->max('version_number') ?? 0) + 1;
        $file = $validated['file'];
        $storedPath = $this->fileService->store($file, (int) $document->year);

        try {
            $version = DB::transaction(function () use ($document, $nextVersion, $storedPath, $file, $actor, $validated) {
                $version = DocumentVersion::create([
                    'document_id' => $document->id,
                    'version_number' => $nextVersion,
                    'file_path' => $storedPath,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'notes' => $validated['notes'] ?? null,
                    'uploaded_by' => $actor->id,
                ]);

                $document->updated_by = $actor->id;
                $document->save();

                $this->log(
                    actor: $actor,
                    action: 'upload_version',
                    subject: $version,
                    description: sprintf(
                        'User mengunggah versi baru (v%d) untuk dokumen "%s"',
                        $version->version_number,
                        $document->title
                    ),
                );

                return $version;
            });
        } catch (Throwable $e) {
            $this->fileService->delete($storedPath);
            throw $e;
        }

        $this->sendVersionUploadedNotification($document, $version, $actor);

        return $version;
    }

    /**
     * Hapus versi dokumen. Versi awal (v1) dilindungi agar dokumen tidak pernah tanpa file.
     * File fisik ikut dihapus.
     */
    public function removeVersion(Document $document, DocumentVersion $version, User $actor): void
    {
        if ($version->version_number === 1) {
            throw new \InvalidArgumentException('Versi awal dokumen tidak dapat dihapus.');
        }

        DB::transaction(function () use ($document, $version, $actor) {
            $this->log(
                actor: $actor,
                action: 'delete_version',
                subject: $version,
                description: sprintf(
                    'User menghapus versi (v%d) dari dokumen "%s"',
                    $version->version_number,
                    $document->title
                ),
            );

            $this->fileService->delete($version->file_path);
            $version->delete();
        });
    }

    protected function log(User $actor, string $action, object $subject, string $description, ?array $metadata = null): void
    {
        AuditLog::create([
            'user_id' => $actor->id,
            'action' => $action,
            'auditable_type' => $subject::class,
            'auditable_id' => $subject->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    protected function sendDocumentCreatedNotification(Document $document, User $actor): void
    {
        $users = User::where('is_active', true)
            ->where('id', '!=', $actor->id)
            ->get();

        foreach ($users as $user) {
            $user->notify(new DocumentCreatedNotification($document, $actor->name));
        }
    }

    protected function sendDocumentUpdatedNotification(Document $document, User $actor, array $changedFields): void
    {
        $users = User::where('is_active', true)
            ->where('id', '!=', $actor->id)
            ->get();

        foreach ($users as $user) {
            $user->notify(new DocumentUpdatedNotification($document, $actor->name, $changedFields));
        }
    }

    protected function sendVersionUploadedNotification(Document $document, DocumentVersion $version, User $actor): void
    {
        $users = User::where('is_active', true)
            ->where('id', '!=', $actor->id)
            ->get();

        foreach ($users as $user) {
            $user->notify(new DocumentVersionUploadedNotification($document, $version, $actor->name));
        }
    }
}
