<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Favorite;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function makeUser(string $role = 'VIEWER'): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', $role)->firstOrFail());
        return $user;
    }

    public function test_toggle_adds_document_to_favorites(): void
    {
        $user = $this->makeUser();
        $document = Document::factory()->active()->create();

        $response = $this->actingAs($user)
            ->post(route('favorites.toggle', $document));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }

    public function test_toggle_removes_document_from_favorites(): void
    {
        $user = $this->makeUser();
        $document = Document::factory()->active()->create();
        Favorite::create(['user_id' => $user->id, 'document_id' => $document->id]);

        $this->actingAs($user)
            ->post(route('favorites.toggle', $document));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }

    public function test_index_lists_only_own_favorites(): void
    {
        $user = $this->makeUser();
        $other = $this->makeUser();

        $myDoc = Document::factory()->active()->create(['title' => 'Dokumen Saya']);
        $theirDoc = Document::factory()->active()->create(['title' => 'Dokumen Orang Lain']);

        Favorite::create(['user_id' => $user->id, 'document_id' => $myDoc->id]);
        Favorite::create(['user_id' => $other->id, 'document_id' => $theirDoc->id]);

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('Dokumen Saya')
            ->assertDontSee('Dokumen Orang Lain');
    }

    public function test_user_can_delete_own_favorite(): void
    {
        $user = $this->makeUser();
        $document = Document::factory()->active()->create();
        $favorite = Favorite::create(['user_id' => $user->id, 'document_id' => $document->id]);

        $response = $this->actingAs($user)
            ->delete(route('favorites.destroy', $favorite));

        $response->assertRedirect(route('favorites.index'));
        $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
    }

    public function test_user_cannot_delete_others_favorite(): void
    {
        $user = $this->makeUser();
        $other = $this->makeUser();
        $document = Document::factory()->active()->create();
        $favorite = Favorite::create(['user_id' => $other->id, 'document_id' => $document->id]);

        $this->actingAs($user)
            ->delete(route('favorites.destroy', $favorite))
            ->assertForbidden();
    }
}