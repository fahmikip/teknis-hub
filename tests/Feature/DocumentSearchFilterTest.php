<?php

namespace Tests\Feature;

use App\Enums\AccessLevel;
use App\Enums\DocumentStatus;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\Stage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentSearchFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function admin(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', 'ADMIN')->firstOrFail());

        return $user;
    }

    public function test_search_matches_description(): void
    {
        $user = $this->admin();
        Document::factory()->create(['title' => 'Dokumen Aturan Pemilu', 'description' => 'Berisi aturan penyelenggaraan pemilu khusus']);
        Document::factory()->create(['title' => 'Dokumen Tidak Relevan', 'description' => 'Deskripsi acak tidak relevan']);

        $this->actingAs($user)
            ->get(route('documents.index', ['q' => 'penyelenggaraan']))
            ->assertOk()
            ->assertSee('Dokumen Aturan Pemilu')
            ->assertDontSee('Dokumen Tidak Relevan');
    }

    public function test_search_matches_keywords(): void
    {
        $user = $this->admin();
        Document::factory()->create(['title' => 'Dokumen Surat Edaran', 'keywords' => 'surat, edaran, teknis']);
        Document::factory()->create(['title' => 'Dokumen Pedoman', 'keywords' => 'pedoman, umum']);

        $this->actingAs($user)
            ->get(route('documents.index', ['q' => 'edaran']))
            ->assertOk()
            ->assertSee('Dokumen Surat Edaran')
            ->assertDontSee('Dokumen Pedoman');
    }

    public function test_search_matches_relationship_name(): void
    {
        $user = $this->admin();
        $category = Category::factory()->create(['name' => 'Logistik Pemilu']);
        Document::factory()->create(['title' => 'Dokumen Terkait Logistik', 'category_id' => $category->id]);
        Document::factory()->create(['title' => 'Dokumen Lainnya']);

        $this->actingAs($user)
            ->get(route('documents.index', ['q' => 'Logistik']))
            ->assertOk()
            ->assertSee('Dokumen Terkait Logistik')
            ->assertDontSee('Dokumen Lainnya');
    }

    public function test_filter_by_access_level(): void
    {
        $user = $this->admin();
        Document::factory()->create(['title' => 'Dokumen Internal', 'access_level' => AccessLevel::Internal]);
        Document::factory()->create(['title' => 'Dokumen Publik', 'access_level' => AccessLevel::Public]);

        $this->actingAs($user)
            ->get(route('documents.index', ['access_level' => 'internal']))
            ->assertOk()
            ->assertSee('Dokumen Internal')
            ->assertDontSee('Dokumen Publik');
    }

    public function test_filter_by_date_range(): void
    {
        $user = $this->admin();
        Document::factory()->create(['title' => 'Dokumen Januari', 'document_date' => '2024-01-15']);
        Document::factory()->create(['title' => 'Dokumen Maret', 'document_date' => '2024-03-15']);

        $this->actingAs($user)
            ->get(route('documents.index', ['date_from' => '2024-02-01', 'date_to' => '2024-04-30']))
            ->assertOk()
            ->assertSee('Dokumen Maret')
            ->assertDontSee('Dokumen Januari');
    }

    public function test_archived_page_lists_soft_deleted_documents(): void
    {
        $user = $this->admin();
        Document::factory()->create(['title' => 'Dokumen Aktif']);
        $deleted = Document::factory()->create(['title' => 'Dokumen Terarsip']);
        $deleted->delete();

        $this->actingAs($user)
            ->get(route('documents.archived'))
            ->assertOk()
            ->assertSee('Dokumen Terarsip')
            ->assertDontSee('Dokumen Aktif');
    }

    public function test_recent_page_lists_latest_updated_documents(): void
    {
        $user = $this->admin();
        $old = Document::factory()->create(['title' => 'Dokumen Lama']);
        $old->update(['updated_at' => now()->subDays(5)]);

        $new = Document::factory()->create(['title' => 'Dokumen Baru']);

        $response = $this->actingAs($user)->get(route('documents.recent'))->assertOk();

        $this->assertStringContainsString('Dokumen Baru', $response->getContent());
    }

    public function test_restore_brings_back_soft_deleted_document(): void
    {
        $user = $this->admin();
        $deleted = Document::factory()->create(['title' => 'Dokumen Dipulihkan']);
        $deleted->delete();

        $this->assertTrue($deleted->trashed());

        $this->actingAs($user)
            ->put(route('documents.restore', $deleted->id))
            ->assertRedirect(route('documents.archived'));

        $this->assertFalse($deleted->fresh()->trashed());
        $this->assertDatabaseHas('documents', ['id' => $deleted->id, 'deleted_at' => null]);
    }

    public function test_export_csv_returns_file(): void
    {
        $user = $this->admin();
        $category = Category::factory()->create(['name' => 'Surat Edaran']);
        $type = DocumentType::factory()->create(['name' => 'Edaran', 'slug' => 'edaran']);
        Document::factory()->create([
            'title' => 'Edaran Teknis 2024',
            'document_number' => '001/DT/2024',
            'category_id' => $category->id,
            'document_type_id' => $type->id,
            'year' => 2024,
        ]);

        $response = $this->actingAs($user)
            ->get(route('documents.export'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $this->assertStringContainsString('Edaran Teknis 2024', $response->streamedContent());
        $this->assertStringContainsString('Judul,Nomor', $response->streamedContent());
    }

    public function test_per_page_option_is_respected(): void
    {
        $user = $this->admin();
        Document::factory()->count(30)->create();

        $response = $this->actingAs($user)
            ->get(route('documents.index', ['per_page' => 25]))
            ->assertOk();

        $this->assertCount(25, $response->viewData('documents')->items());
    }

    public function test_sort_direction_toggle(): void
    {
        $user = $this->admin();
        Document::factory()->create(['title' => 'AAA Judul', 'year' => 2023]);
        Document::factory()->create(['title' => 'BBB Judul', 'year' => 2024]);

        $response = $this->actingAs($user)
            ->get(route('documents.index', ['sort' => 'year', 'direction' => 'asc']))
            ->assertOk();

        $this->assertStringContainsString('direction=desc', $response->getContent());
    }
}