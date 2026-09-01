<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTypeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function makeUser(string $role = 'ADMIN'): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', $role)->firstOrFail());
        return $user;
    }

    public function test_index_shows_document_types(): void
    {
        $user = $this->makeUser();
        DocumentType::factory()->create(['name' => 'Keputusan']);

        $this->actingAs($user)
            ->get(route('document-types.index'))
            ->assertOk()
            ->assertSee('Keputusan')
            ->assertSee('Tambah Jenis Dokumen');
    }

    public function test_index_query_searches_by_name(): void
    {
        $user = $this->makeUser();
        DocumentType::factory()->create(['name' => 'Surat']);
        DocumentType::factory()->create(['name' => 'Laporan']);

        $this->actingAs($user)
            ->get(route('document-types.index', ['q' => 'Surat']))
            ->assertOk()
            ->assertSee('Surat')
            ->assertDontSee('Laporan');
    }

    public function test_admin_can_see_create_page(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get(route('document-types.create'))
            ->assertOk()
            ->assertSee('Tambah Jenis Dokumen');
    }

    public function test_admin_can_create_document_type(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->post(route('document-types.store'), [
            'name' => 'Risalah',
            'description' => 'Dokumen risalah',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('document-types.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('document_types', [
            'name' => 'Risalah',
            'slug' => 'risalah',
            'is_active' => true,
        ]);
    }

    public function test_duplicate_document_type_name_is_rejected(): void
    {
        $user = $this->makeUser();
        DocumentType::factory()->create(['name' => 'Surat']);

        $this->from(route('document-types.create'))
            ->actingAs($user)
            ->post(route('document-types.store'), ['name' => 'Surat'])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_see_edit_page(): void
    {
        $user = $this->makeUser();
        $type = DocumentType::factory()->create();

        $this->actingAs($user)
            ->get(route('document-types.edit', $type))
            ->assertOk()
            ->assertSee($type->name);
    }

    public function test_admin_can_update_document_type(): void
    {
        $user = $this->makeUser();
        $type = DocumentType::factory()->create(['name' => 'Lama']);

        $response = $this->actingAs($user)->put(route('document-types.update', $type), [
            'name' => 'Sertifikat',
            'description' => 'Diperbarui',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('document-types.index'));
        $this->assertDatabaseHas('document_types', [
            'id' => $type->id,
            'name' => 'Sertifikat',
            'slug' => 'sertifikat',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_unused_document_type(): void
    {
        $user = $this->makeUser();
        $type = DocumentType::factory()->create();

        $response = $this->actingAs($user)->delete(route('document-types.destroy', $type));

        $response->assertRedirect(route('document-types.index'));
        $this->assertDatabaseMissing('document_types', ['id' => $type->id]);
    }

    public function test_document_type_in_use_cannot_be_deleted(): void
    {
        $user = $this->makeUser();
        $type = DocumentType::factory()->create();
        Document::factory()->create(['document_type_id' => $type->id]);

        $response = $this->actingAs($user)->delete(route('document-types.destroy', $type));

        $response->assertRedirect(route('document-types.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('document_types', ['id' => $type->id]);
    }

    public function test_viewer_cannot_manage_document_types(): void
    {
        $user = $this->makeUser('VIEWER');

        $this->actingAs($user)->get(route('document-types.index'))->assertForbidden();
        $this->actingAs($user)->get(route('document-types.create'))->assertForbidden();
    }
}