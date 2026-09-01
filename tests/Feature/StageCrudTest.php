<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Role;
use App\Models\Stage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageCrudTest extends TestCase
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

    public function test_index_shows_stages(): void
    {
        $user = $this->makeUser();
        Stage::factory()->create(['name' => 'Kampanye']);

        $this->actingAs($user)
            ->get(route('stages.index'))
            ->assertOk()
            ->assertSee('Kampanye')
            ->assertSee('Tambah Tahapan');
    }

    public function test_index_query_searches_by_name(): void
    {
        $user = $this->makeUser();
        Stage::factory()->create(['name' => 'Pemungutan Suara']);
        Stage::factory()->create(['name' => 'Evaluasi']);

        $this->actingAs($user)
            ->get(route('stages.index', ['q' => 'Pemungutan']))
            ->assertOk()
            ->assertSee('Pemungutan Suara')
            ->assertDontSee('Evaluasi');
    }

    public function test_index_filters_by_election_type(): void
    {
        $user = $this->makeUser();
        Stage::factory()->create(['name' => 'Kampanye', 'election_type' => 'pemilu']);
        Stage::factory()->create(['name' => 'Pelaksanaan', 'election_type' => 'general']);

        $this->actingAs($user)
            ->get(route('stages.index', ['election_type' => 'pemilu']))
            ->assertOk()
            ->assertSee('Kampanye')
            ->assertDontSee('Pelaksanaan');
    }

    public function test_admin_can_see_create_page(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get(route('stages.create'))
            ->assertOk()
            ->assertSee('Tambah Tahapan');
    }

    public function test_admin_can_create_stage(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->post(route('stages.store'), [
            'name' => 'Monitoring',
            'election_type' => 'pemilu',
            'description' => 'Tahapan kampanye',
            'sort_order' => '3',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('stages.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stages', [
            'name' => 'Monitoring',
            'slug' => 'monitoring',
            'election_type' => 'pemilu',
            'sort_order' => 3,
            'is_active' => true,
        ]);
    }

    public function test_election_type_is_required(): void
    {
        $user = $this->makeUser();

        $this->from(route('stages.create'))
            ->actingAs($user)
            ->post(route('stages.store'), ['name' => 'Kampanye'])
            ->assertSessionHasErrors('election_type');
    }

    public function test_admin_can_see_edit_page(): void
    {
        $user = $this->makeUser();
        $stage = Stage::factory()->create();

        $this->actingAs($user)
            ->get(route('stages.edit', $stage))
            ->assertOk()
            ->assertSee($stage->name);
    }

    public function test_admin_can_update_stage(): void
    {
        $user = $this->makeUser();
        $stage = Stage::factory()->create(['name' => 'Lama', 'election_type' => 'general']);

        $response = $this->actingAs($user)->put(route('stages.update', $stage), [
            'name' => 'Verifikasi',
            'election_type' => 'pilkada',
            'description' => 'Diperbarui',
            'sort_order' => '5',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('stages.index'));
        $this->assertDatabaseHas('stages', [
            'id' => $stage->id,
            'name' => 'Verifikasi',
            'slug' => 'verifikasi',
            'election_type' => 'pilkada',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_unused_stage(): void
    {
        $user = $this->makeUser();
        $stage = Stage::factory()->create();

        $response = $this->actingAs($user)->delete(route('stages.destroy', $stage));

        $response->assertRedirect(route('stages.index'));
        $this->assertDatabaseMissing('stages', ['id' => $stage->id]);
    }

    public function test_stage_in_use_cannot_be_deleted(): void
    {
        $user = $this->makeUser();
        $stage = Stage::factory()->create();
        Document::factory()->create(['stage_id' => $stage->id]);

        $response = $this->actingAs($user)->delete(route('stages.destroy', $stage));

        $response->assertRedirect(route('stages.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('stages', ['id' => $stage->id]);
    }

    public function test_viewer_cannot_manage_stages(): void
    {
        $user = $this->makeUser('VIEWER');

        $this->actingAs($user)->get(route('stages.index'))->assertForbidden();
        $this->actingAs($user)->get(route('stages.create'))->assertForbidden();
    }
}