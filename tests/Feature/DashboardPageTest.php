<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function makeSuperAdmin(): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'SUPER ADMIN')->firstOrFail());
        return $user;
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Dokumen Terbaru');
        $response->assertSee('TeknisHub');
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_sidebar_navigation_links_are_rendered(): void
    {
        $user = $this->makeSuperAdmin();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Semua Dokumen');
        $response->assertSee('Favorit');
        $response->assertSee('Pengaturan');
    }
}
