<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentListTest extends TestCase
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

    public function test_index_shows_documents(): void
    {
        $user = $this->makeUser('ADMIN');
        Document::factory()->count(3)->create();

        $this->actingAs($user)
            ->get(route('documents.index'))
            ->assertOk()
            ->assertSee('Kelola seluruh dokumen Divisi Teknis');
    }

    public function test_index_paginates_documents(): void
    {
        $user = $this->makeUser('ADMIN');
        Document::factory()->count(20)->create();

        $response = $this->actingAs($user)->get(route('documents.index'));
        $response->assertOk();

        $this->assertCount(15, $response->viewData('documents')->items());
        $response->assertViewHas('documents', function ($docs) {
            return $docs->total() === 20;
        });
    }

    public function test_basic_search_filters_by_title(): void
    {
        $user = $this->makeUser('ADMIN');
        Document::factory()->create(['title' => 'Dokumen Pemilu Khusus']);
        Document::factory()->create(['title' => 'Random Judul Lain']);

        $this->actingAs($user)
            ->get(route('documents.index', ['q' => 'Pemilu']))
            ->assertOk()
            ->assertSee('Dokumen Pemilu Khusus')
            ->assertDontSee('Random Judul Lain');
    }

    public function test_filter_by_year(): void
    {
        $user = $this->makeUser('ADMIN');
        Document::factory()->create(['title' => 'Dokumen Tahun 2024', 'year' => 2024]);
        Document::factory()->create(['title' => 'Dokumen Tahun 2025', 'year' => 2025]);

        $this->actingAs($user)
            ->get(route('documents.index', ['year' => 2024]))
            ->assertOk()
            ->assertSee('Dokumen Tahun 2024')
            ->assertDontSee('Dokumen Tahun 2025');
    }

    public function test_index_does_not_show_soft_deleted_documents(): void
    {
        $user = $this->makeUser('ADMIN');
        Document::factory()->create(['title' => 'Dokumen Aktif']);
        $archived = Document::factory()->create(['title' => 'Dokumen Terarsip']);
        $archived->delete();

        $this->actingAs($user)
            ->get(route('documents.index'))
            ->assertOk()
            ->assertSee('Dokumen Aktif')
            ->assertDontSee('Dokumen Terarsip');
    }
}
