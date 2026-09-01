<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DocumentType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentCreateTest extends TestCase
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

    public function test_admin_can_see_create_page(): void
    {
        $user = $this->makeUser('ADMIN');

        $this->actingAs($user)
            ->get(route('documents.create'))
            ->assertOk()
            ->assertSee('Tambah Dokumen');
    }

    public function test_admin_can_create_document_with_initial_version(): void
    {
        $user = $this->makeUser('ADMIN');

        $category = Category::factory()->create();
        $type = DocumentType::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('documents.store'), [
                'title' => 'Keputusan KPU Kabupaten Nomor 123 Tahun 2026',
                'document_number' => '123/ABC/2026',
                'document_type_id' => $type->id,
                'category_id' => $category->id,
                'stage_id' => '',
                'year' => 2026,
                'document_date' => '2026-05-01',
                'status' => 'active',
                'access_level' => 'internal',
                'description' => 'Deskripsi dokumen',
                'keywords' => 'keputusan, kpu',
                'file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('keputusan.pdf', '%PDF-1.4 fake pdf'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', ['title' => 'Keputusan KPU Kabupaten Nomor 123 Tahun 2026']);
        $document = \App\Models\Document::where('title', 'Keputusan KPU Kabupaten Nomor 123 Tahun 2026')->firstOrFail();

        $this->assertDatabaseHas('document_versions', [
            'document_id' => $document->id,
            'version_number' => 1,
            'original_filename' => 'keputusan.pdf',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create_document',
            'auditable_id' => $document->id,
        ]);
    }

    public function test_redirects_to_document_detail_on_success(): void
    {
        $user = $this->makeUser('ADMIN');
        $category = Category::factory()->create();
        $type = DocumentType::factory()->create();

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'title' => 'Dokumen Resmi',
            'document_type_id' => $type->id,
            'category_id' => $category->id,
            'year' => 2026,
            'status' => 'active',
            'access_level' => 'internal',
            'file' => \Illuminate\Http\UploadedFile::fake()->createWithContent('resmi.pdf', '%PDF-1.4 fake pdf'),
        ]);

        $document = \App\Models\Document::where('title', 'Dokumen Resmi')->firstOrFail();
        $response->assertRedirect(route('documents.show', $document));
    }
}
