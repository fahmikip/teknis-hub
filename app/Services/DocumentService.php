<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class DocumentService
{
    public function __construct(
        protected DocumentFileService $fileService
    ) {
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

        return $document;
    }

    /**
     * Update metadata dokumen (tanpa mengganti file) dalam satu transaksi.
     */
    public function updateDocument(Document $document, array $validated, User $actor): Document
    {
        return DB::transaction(function () use ($document, $validated, $actor) {
            $changed = [];
            foreach ($validated as $key => $value) {
                if ($this->valuesDiffer($document->{$key}, $value)) {
                    $changed[] = $key;
                }
            }

            $document->update(array_merge($validated, ['updated_by' => $actor->id]));

            $this->log(
                actor: $actor,
                action: 'update_document',
                subject: $document,
                description: sprintf('User memperbarui dokumen "%s"', $document->title),
                metadata: ['changed' => array_values(array_unique($changed))],
            );

            return $document;
        });
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
}
