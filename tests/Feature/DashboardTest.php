<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_document_count_and_latest_documents(): void
    {
        $user = $this->makeUser();
        $doc = Document::factory()->active()->create(['title' => 'Surat Edaran Teknis']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Surat Edaran Teknis');
    }

    public function test_dashboard_shows_recent_activity(): void
    {
        $user = $this->makeUser('ADMIN');
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'create_document',
            'description' => 'User membuat dokumen "Laporan Akhir"',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Laporan Akhir');
    }

    public function test_dashboard_shows_own_favorites(): void
    {
        $user = $this->makeUser();
        $favDoc = Document::factory()->active()->create(['title' => 'Dokumen Pilihan']);

        $user->favorites()->create(['document_id' => $favDoc->id]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dokumen Pilihan');
    }
}