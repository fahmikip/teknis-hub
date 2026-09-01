<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
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
        $user->roles()->attach(\App\Models\Role::where('name', $role)->firstOrFail());
        return $user;
    }

    public function test_index_shows_categories(): void
    {
        $user = $this->makeUser();
        Category::factory()->create(['name' => 'Regulasi']);

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Regulasi')
            ->assertSee('Tambah Kategori');
    }

    public function test_index_query_searches_by_name(): void
    {
        $user = $this->makeUser();
        Category::factory()->create(['name' => 'Keputusan']);
        Category::factory()->create(['name' => 'Formulir']);

        $this->actingAs($user)
            ->get(route('categories.index', ['q' => 'Keputusan']))
            ->assertOk()
            ->assertSee('Keputusan')
            ->assertDontSee('Formulir');
    }

    public function test_admin_can_see_create_page(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get(route('categories.create'))
            ->assertOk()
            ->assertSee('Tambah Kategori');
    }

    public function test_admin_can_create_category(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Dokumen Baru',
            'description' => 'Deskripsi kategori',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'name' => 'Dokumen Baru',
            'slug' => 'dokumen-baru',
            'is_active' => true,
        ]);
    }

    public function test_duplicate_category_name_is_rejected(): void
    {
        $user = $this->makeUser();
        Category::factory()->create(['name' => 'Regulasi']);

        $this->from(route('categories.create'))
            ->actingAs($user)
            ->post(route('categories.store'), ['name' => 'Regulasi'])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_see_edit_page(): void
    {
        $user = $this->makeUser();
        $category = Category::factory()->create();

        $this->actingAs($user)
            ->get(route('categories.edit', $category))
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_admin_can_update_category(): void
    {
        $user = $this->makeUser();
        $category = Category::factory()->create(['name' => 'Lama']);

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Baru',
            'description' => 'Diperbarui',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Baru',
            'slug' => 'baru',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_unused_category(): void
    {
        $user = $this->makeUser();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $user = $this->makeUser();
        $category = Category::factory()->create();
        Document::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_viewer_cannot_manage_categories(): void
    {
        $user = $this->makeUser('VIEWER');

        $this->actingAs($user)->get(route('categories.index'))->assertForbidden();
        $this->actingAs($user)->get(route('categories.create'))->assertForbidden();
    }
}
