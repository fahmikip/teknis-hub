<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
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
        $user->roles()->attach(Role::where('name', $role)->firstOrFail());
        return $user;
    }

    public function test_admin_can_see_users_index(): void
    {
        User::factory()->create(['name' => 'Budi Santoso']);

        $this->actingAs($this->makeUser())
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Tambah Pengguna');
    }

    public function test_index_searches_users_by_name_or_email(): void
    {
        User::factory()->create(['name' => 'Ani Wulandari', 'email' => 'ani@test.dev']);
        User::factory()->create(['name' => 'Rudi Hidayat']);

        $this->actingAs($this->makeUser())
            ->get(route('users.index', ['q' => 'ani']))
            ->assertOk()
            ->assertSee('Ani Wulandari')
            ->assertDontSee('Rudi Hidayat');
    }

    public function test_index_filters_users_by_role(): void
    {
        $operatorRole = Role::where('name', 'OPERATOR')->firstOrFail();
        $user = $this->makeUser();
        User::factory()->create()->roles()->attach($operatorRole);

        $this->actingAs($user)
            ->get(route('users.index', ['role_id' => $operatorRole->id]))
            ->assertOk();
    }

    public function test_admin_can_create_user(): void
    {
        $operatorRole = Role::where('name', 'OPERATOR')->firstOrFail();

        $response = $this->actingAs($this->makeUser())->post(route('users.store'), [
            'name' => 'Dewi Lestari',
            'username' => 'dewi',
            'email' => 'dewi@test.dev',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'is_active' => '1',
            'roles' => [$operatorRole->id],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Dewi Lestari',
            'username' => 'dewi',
            'email' => 'dewi@test.dev',
            'is_active' => true,
        ]);
        $created = User::where('email', 'dewi@test.dev')->firstOrFail();
        $this->assertTrue($created->hasRole('OPERATOR'));
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'sama@test.dev']);

        $this->from(route('users.create'))
            ->actingAs($this->makeUser())
            ->post(route('users.store'), [
                'name' => 'Orang Baru',
                'username' => 'orba',
                'email' => 'sama@test.dev',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_see_edit_page(): void
    {
        $target = User::factory()->create(['name' => 'Eko Prasetyo']);

        $this->actingAs($this->makeUser())
            ->get(route('users.edit', $target))
            ->assertOk()
            ->assertSee('Eko Prasetyo');
    }

    public function test_admin_can_update_user(): void
    {
        $target = User::factory()->create(['name' => 'Lama Nama', 'username' => 'lama']);

        $response = $this->actingAs($this->makeUser())
            ->put(route('users.update', $target), [
                'name' => 'Nama Baru',
                'username' => $target->username,
                'email' => $target->email,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Nama Baru',
        ]);
    }

    public function test_admin_can_deactivate_user(): void
    {
        $target = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->makeUser())
            ->delete(route('users.destroy', $target));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_cannot_deactivate_super_admin(): void
    {
        $superAdmin = Role::where('name', 'SUPER ADMIN')->firstOrFail();
        $target = User::whereHas('roles', fn ($q) => $q->whereKey($superAdmin->id))->firstOrFail();

        $this->actingAs($this->makeUser())
            ->delete(route('users.destroy', $target))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->delete(route('users.destroy', $user))
            ->assertForbidden();
    }

    public function test_viewer_cannot_access_users(): void
    {
        $this->actingAs($this->makeUser('VIEWER'))
            ->get(route('users.index'))
            ->assertForbidden();
    }
}