<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
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

    public function test_admin_can_see_audit_logs(): void
    {
        $admin = $this->makeUser();
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'create_document',
            'auditable_type' => null,
            'auditable_id' => null,
            'description' => 'User membuat dokumen "Surat Undangan"',
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($admin)
            ->get(route('audit-logs.index'))
            ->assertOk()
            ->assertSee('Surat Undangan')
            ->assertSee('create_document');
    }

    public function test_operator_cannot_access_audit_logs(): void
    {
        $this->actingAs($this->makeUser('OPERATOR'))
            ->get(route('audit-logs.index'))
            ->assertForbidden();
    }

    public function test_viewer_cannot_access_audit_logs(): void
    {
        $this->actingAs($this->makeUser('VIEWER'))
            ->get(route('audit-logs.index'))
            ->assertForbidden();
    }

    public function test_index_filters_by_action(): void
    {
        $admin = $this->makeUser();
        AuditLog::create(['user_id' => $admin->id, 'action' => 'create_document', 'description' => 'Aksi buat']);
        AuditLog::create(['user_id' => $admin->id, 'action' => 'archive_document', 'description' => 'Aksi arsip']);

        $this->actingAs($admin)
            ->get(route('audit-logs.index', ['action' => 'create_document']))
            ->assertOk()
            ->assertSee('Aksi buat')
            ->assertDontSee('Aksi arsip');
    }

    public function test_index_searches_by_description(): void
    {
        $admin = $this->makeUser();
        AuditLog::create(['user_id' => $admin->id, 'action' => 'create_document', 'description' => 'membuat Berita Acara final']);
        AuditLog::create(['user_id' => $admin->id, 'action' => 'create_document', 'description' => 'membuat Notulen rapat']);

        $this->actingAs($admin)
            ->get(route('audit-logs.index', ['q' => 'Berita Acara']))
            ->assertOk()
            ->assertSee('Berita Acara final')
            ->assertDontSee('Notulen rapat');
    }
}