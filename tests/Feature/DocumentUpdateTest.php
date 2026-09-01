<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function makeUser(?string $role = 'ADMIN'): User
    {
        $user = User::factory()->create(['is_active' => true]);
        if ($role) {
            $user->roles()->attach(\App\Models\Role::where('name', $role)->firstOrFail());
        }
        return $user;
    }

    protected function makeDocument(User $creator): Document
    {
        return Document::factory()->create([
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);
    }

    public function test_admin_can_see_edit_page(): void
    {
        $user = $this->makeUser('ADMIN');
        $document = $this->makeDocument($user);

        $this->actingAs($user)
            ->get(route('documents.edit', $document))
            ->assertOk()
            ->assertSee('Edit Dokumen');
    }

    public function test_admin_can_update_metadata(): void
    {
        $user = $this->makeUser('ADMIN');
        $document = $this->makeDocument($user);
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->put(route('documents.update', $document), [
            'title' => 'Judul Baru Dokumen',
            'document_number' => '999/XYZ/2026',
            'document_type_id' => $document->document_type_id,
            'category_id' => $category->id,
            'stage_id' => '',
            'year' => 2026,
            'document_date' => '2026-06-01',
            'status' => 'active',
            'access_level' => 'restricted',
            'description' => 'Deskripsi diperbarui',
            'keywords' => 'baru, dokumen',
        ]);

        $response->assertRedirect(route('documents.show', $document));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Judul Baru Dokumen',
            'category_id' => $category->id,
            'updated_by' => $user->id,
        ]);
    }

    public function test_update_records_audit_log_with_changed_fields(): void
    {
        $user = $this->makeUser('ADMIN');
        $document = $this->makeDocument($user);

        $this->actingAs($user)->put(route('documents.update', $document), [
            'title' => 'Judul Berubah',
            'document_type_id' => $document->document_type_id,
            'category_id' => $document->category_id,
            'year' => 2026,
            'status' => 'active',
            'access_level' => 'internal',
        ]);

        $log = \App\Models\AuditLog::where('action', 'update_document')
            ->where('auditable_id', $document->id)->firstOrFail();

        $this->assertArrayHasKey('changed', $log->metadata);
        $this->assertContains('title', $log->metadata['changed']);
    }
}
