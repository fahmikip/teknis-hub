<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', $role)->firstOrFail());
        return $user;
    }

    public function test_guest_cannot_access_documents(): void
    {
        $this->get(route('documents.index'))->assertRedirect(route('login'));
        $this->get(route('documents.create'))->assertRedirect(route('login'));
    }

    public function test_viewer_can_view_documents(): void
    {
        $user = $this->makeUser('VIEWER');
        Document::factory()->create(['title' => 'Dokumen Viewer Bisa Lihat']);

        $this->actingAs($user)->get(route('documents.index'))->assertOk();
    }

    public function test_viewer_cannot_create_document(): void
    {
        $user = $this->makeUser('VIEWER');

        $this->actingAs($user)->get(route('documents.create'))->assertStatus(403);
        $this->actingAs($user)
            ->post(route('documents.store'), $this->validPayload())
            ->assertStatus(403);
    }

    public function test_viewer_cannot_update_document(): void
    {
        $user = $this->makeUser('VIEWER');
        $document = Document::factory()->create();

        $this->actingAs($user)->get(route('documents.edit', $document))->assertStatus(403);
        $this->actingAs($user)
            ->put(route('documents.update', $document), $this->validPayload())
            ->assertStatus(403);
    }

    public function test_viewer_cannot_archive_document(): void
    {
        $user = $this->makeUser('VIEWER');
        $document = Document::factory()->create();

        $this->actingAs($user)->delete(route('documents.destroy', $document))->assertStatus(403);
        $this->assertNotSoftDeleted('documents', ['id' => $document->id]);
    }

    public function test_operator_can_create_and_edit(): void
    {
        $operator = $this->makeUser('OPERATOR');
        $document = Document::factory()->create();

        $this->actingAs($operator)->get(route('documents.create'))->assertOk();
        $this->actingAs($operator)->get(route('documents.edit', $document))->assertOk();
    }

    public function test_operator_cannot_archive_document(): void
    {
        $operator = $this->makeUser('OPERATOR');
        $document = Document::factory()->create();

        $this->actingAs($operator)->delete(route('documents.destroy', $document))->assertStatus(403);
    }

    public function test_admin_can_archive_document(): void
    {
        $admin = $this->makeUser('ADMIN');
        $document = Document::factory()->create();

        $this->actingAs($admin)->delete(route('documents.destroy', $document))
            ->assertRedirect(route('documents.index'));

        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }

    protected function validPayload(): array
    {
        return [
            'title' => 'Dokumen Uji',
            'document_type_id' => \App\Models\DocumentType::factory()->create()->id,
            'category_id' => \App\Models\Category::factory()->create()->id,
            'year' => 2026,
            'status' => 'active',
            'access_level' => 'internal',
        ];
    }
}
