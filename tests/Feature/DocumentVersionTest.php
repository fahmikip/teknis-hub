<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentVersionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('local');
    }

    protected function makeUser(string $role = 'ADMIN'): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', $role)->firstOrFail());
        return $user;
    }

    protected function makeDocumentWithFile(): Document
    {
        $document = Document::factory()->active()->create();
        Storage::disk('local')->put('documents/test-file.pdf', '%PDF-1.4 test');
        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => 1,
            'file_path' => 'documents/test-file.pdf',
            'original_filename' => 'surat-umum.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1000,
            'notes' => 'Versi awal dokumen',
            'uploaded_by' => $document->created_by,
        ]);

        return $document->fresh();
    }

    public function test_download_returns_file(): void
    {
        $document = $this->makeDocumentWithFile();

        $response = $this->actingAs($this->makeUser())
            ->get(route('documents.download', $document));

        $response->assertOk();
        $this->assertStringContainsString('%PDF-1.4 test', $response->streamedContent());
    }

    public function test_download_returns_404_when_no_file(): void
    {
        $document = Document::factory()->active()->create();

        $this->actingAs($this->makeUser())
            ->get(route('documents.download', $document))
            ->assertNotFound();
    }

    public function test_preview_renders_file_inline(): void
    {
        $document = $this->makeDocumentWithFile();

        $response = $this->actingAs($this->makeUser())
            ->get(route('documents.preview', $document));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('%PDF-1.4 test', $response->streamedContent());
    }

    public function test_operator_can_upload_new_version(): void
    {
        $document = $this->makeDocumentWithFile();

        $response = $this->actingAs($this->makeUser('OPERATOR'))
            ->post(route('documents.versions.store', $document), [
                'file' => UploadedFile::fake()->create('revisi.pdf', 100, 'application/pdf'),
                'notes' => 'Perbaikan lampiran',
            ]);

        $response->assertRedirect(route('documents.show', $document));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('document_versions', [
            'document_id' => $document->id,
            'version_number' => 2,
            'notes' => 'Perbaikan lampiran',
        ]);
    }

    public function test_viewer_cannot_upload_new_version(): void
    {
        $document = $this->makeDocumentWithFile();

        $this->actingAs($this->makeUser('VIEWER'))
            ->post(route('documents.versions.store', $document), [
                'file' => UploadedFile::fake()->create('revisi.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();
    }

    public function test_viewer_cannot_delete_version(): void
    {
        $document = $this->makeDocumentWithFile();
        $version = $document->versions()->create([
            'version_number' => 2,
            'file_path' => 'documents/v2.pdf',
            'original_filename' => 'revisi.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'notes' => null,
            'uploaded_by' => $document->created_by,
        ]);

        $this->actingAs($this->makeUser('VIEWER'))
            ->delete(route('documents.versions.destroy', [$document, $version]))
            ->assertForbidden();
    }

    public function test_first_version_cannot_be_deleted(): void
    {
        $document = $this->makeDocumentWithFile();
        $version = $document->latestVersion()->firstOrFail();

        $response = $this->actingAs($this->makeUser('OPERATOR'))
            ->delete(route('documents.versions.destroy', [$document, $version]));

        $response->assertRedirect(route('documents.show', $document));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('document_versions', ['id' => $version->id]);
    }

    public function test_operator_can_delete_newer_version(): void
    {
        $document = $this->makeDocumentWithFile();
        $version = $document->versions()->create([
            'version_number' => 2,
            'file_path' => 'documents/v2.pdf',
            'original_filename' => 'revisi.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'notes' => null,
            'uploaded_by' => $document->created_by,
        ]);
        Storage::disk('local')->put($version->file_path, '%PDF v2');

        $response = $this->actingAs($this->makeUser('OPERATOR'))
            ->delete(route('documents.versions.destroy', [$document, $version]));

        $response->assertRedirect(route('documents.show', $document));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('document_versions', ['id' => $version->id]);
        Storage::disk('local')->assertMissing($version->file_path);
    }

    public function test_show_page_displays_version_history(): void
    {
        $document = $this->makeDocumentWithFile();
        $document->versions()->create([
            'version_number' => 2,
            'file_path' => 'documents/v2.pdf',
            'original_filename' => 'revisi-final.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'notes' => 'Revisi final',
            'uploaded_by' => $document->created_by,
        ]);

        $this->actingAs($this->makeUser())
            ->get(route('documents.show', $document))
            ->assertOk()
            ->assertSee('Riwayat Versi')
            ->assertSee('v2')
            ->assertSee('revisi-final.pdf');
    }
}