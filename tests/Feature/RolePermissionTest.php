<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_roles_are_seeded(): void
    {
        $this->assertDatabaseHas('roles', ['name' => 'SUPER ADMIN']);
        $this->assertDatabaseHas('roles', ['name' => 'ADMIN']);
        $this->assertDatabaseHas('roles', ['name' => 'OPERATOR']);
        $this->assertDatabaseHas('roles', ['name' => 'VIEWER']);
    }

    public function test_admin_user_has_super_admin_role(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@teknishub.test'))->firstOrFail();

        $this->assertTrue($admin->hasRole('SUPER ADMIN'));
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $role = Role::where('name', 'SUPER ADMIN')->firstOrFail();

        $this->assertTrue($role->permissions()->where('name', 'manage_users')->exists());
        $this->assertTrue($role->permissions()->where('name', 'create_documents')->exists());
        $this->assertFalse($role->permissions()->count() < 10);
    }

    public function test_viewer_only_has_view_permissions(): void
    {
        $role = Role::where('name', 'VIEWER')->firstOrFail();

        $this->assertTrue($role->permissions()->where('name', 'view_documents')->exists());
        $this->assertFalse($role->permissions()->where('name', 'create_documents')->exists());
        $this->assertFalse($role->permissions()->where('name', 'manage_users')->exists());
    }
}
