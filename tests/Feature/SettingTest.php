<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function makeSuperAdmin(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', 'SUPER ADMIN')->firstOrFail());
        return $user;
    }

    protected function makeUser(string $role = 'ADMIN'): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::where('name', $role)->firstOrFail());
        return $user;
    }

    public function test_super_admin_can_see_settings(): void
    {
        $this->actingAs($this->makeSuperAdmin())
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan')
            ->assertSee('app_name');
    }

    public function test_super_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->makeSuperAdmin())
            ->put(route('settings.update'), [
                'app_name' => 'TeknisHub Baru',
                'institution_name' => 'KPU Kabupaten Baru',
                'institution_region' => 'Kota Contoh',
                'footer_text' => 'Footer baru',
                'max_upload_size' => '40960',
            ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('settings', [
            'key' => 'app_name',
            'value' => 'TeknisHub Baru',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'max_upload_size',
            'value' => '40960',
        ]);
        $this->assertSame('TeknisHub Baru', Setting::get('app_name'));
    }

    public function test_admin_cannot_access_settings(): void
    {
        $this->actingAs($this->makeUser())
            ->get(route('settings.index'))
            ->assertForbidden();
    }

    public function test_viewer_cannot_access_settings(): void
    {
        $this->actingAs($this->makeUser('VIEWER'))
            ->get(route('settings.index'))
            ->assertForbidden();
    }

    public function test_max_upload_size_validates(): void
    {
        $this->from(route('settings.index'))
            ->actingAs($this->makeSuperAdmin())
            ->put(route('settings.update'), [
                'app_name' => 'TeknisHub',
                'max_upload_size' => '100',
            ])
            ->assertSessionHasErrors('max_upload_size');
    }
}