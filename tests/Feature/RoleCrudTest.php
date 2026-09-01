<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleCrudTest extends TestCase
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

    public function test_super_admin_can_see_roles_index(): void
    {
        $this->actingAs($this->makeSuperAdmin())
            ->get(route('roles.index'))
            ->assertOk()
            ->assertSee('SUPER ADMIN')
            ->assertSee('Role &amp; Permission');
    }

    public function test_admin_cannot_access_roles(): void
    {
        $this->actingAs($this->makeUser())
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_create_role(): void
    {
        $permission = Permission::where('name', 'view_documents')->firstOrFail();

        $response = $this->actingAs($this->makeSuperAdmin())
            ->post(route('roles.store'), [
                'name' => 'Auditor',
                'description' => 'Role audit dokumen',
                'permissions' => [$permission->id],
            ]);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'Auditor',
            'slug' => 'auditor',
        ]);

        $role = Role::where('name', 'Auditor')->firstOrFail();
        $this->assertTrue($role->permissions()->whereKey($permission->id)->exists());
    }

    public function test_role_name_must_be_unique(): void
    {
        $this->from(route('roles.create'))
            ->actingAs($this->makeSuperAdmin())
            ->post(route('roles.store'), ['name' => 'ADMIN'])
            ->assertSessionHasErrors('name');
    }

    public function test_super_admin_can_update_role(): void
    {
        $role = Role::create(['name' => 'Auditor', 'slug' => 'auditor', 'label' => 'Auditor']);
        $permission = Permission::where('name', 'view_audit_logs')->firstOrFail();

        $response = $this->actingAs($this->makeSuperAdmin())
            ->put(route('roles.update', $role), [
                'name' => 'Auditor Senior',
                'description' => 'Diperbarui',
                'permissions' => [$permission->id],
            ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Auditor Senior',
            'slug' => 'auditor-senior',
        ]);
        $this->assertTrue($role->permissions()->whereKey($permission->id)->exists());
    }

    public function test_super_admin_can_delete_unused_custom_role(): void
    {
        $role = Role::create(['name' => 'Auditor', 'slug' => 'auditor', 'label' => 'Auditor']);

        $response = $this->actingAs($this->makeSuperAdmin())
            ->delete(route('roles.destroy', $role));

        $response->assertRedirect(route('roles.index'));
        $this->assertSoftDeleted('roles', ['id' => $role->id]);
    }

    public function test_system_role_cannot_be_deleted(): void
    {
        $role = Role::where('name', 'ADMIN')->firstOrFail();

        $this->actingAs($this->makeSuperAdmin())
            ->delete(route('roles.destroy', $role))
            ->assertForbidden();

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_role_in_use_cannot_be_deleted(): void
    {
        $role = Role::create(['name' => 'Auditor', 'slug' => 'auditor', 'label' => 'Auditor']);
        User::factory()->create()->roles()->attach($role);

        $response = $this->actingAs($this->makeSuperAdmin())
            ->delete(route('roles.destroy', $role));

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_viewer_cannot_access_roles(): void
    {
        $this->actingAs($this->makeUser('VIEWER'))
            ->get(route('roles.index'))
            ->assertForbidden();
    }
}