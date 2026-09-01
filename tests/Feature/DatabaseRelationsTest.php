<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\Favorite;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_belongs_to_category_and_document_type(): void
    {
        $document = Document::factory()->create();

        $this->assertNotNull($document->category);
        $this->assertNotNull($document->documentType);
    }

    public function test_document_has_many_versions_and_orders_desc(): void
    {
        $document = Document::factory()->create();
        DocumentVersion::factory()->create(['document_id' => $document->id, 'version_number' => 2]);
        DocumentVersion::factory()->create(['document_id' => $document->id, 'version_number' => 1]);

        $this->assertCount(2, $document->versions);
        $this->assertEquals([2, 1], $document->versions->pluck('version_number')->all());
    }

    public function test_version_number_is_unique_per_document(): void
    {
        $document = Document::factory()->create();
        DocumentVersion::factory()->create(['document_id' => $document->id, 'version_number' => 1]);

        $this->assertThrows(function () use ($document) {
            DocumentVersion::factory()->create(['document_id' => $document->id, 'version_number' => 1]);
        }, \Illuminate\Database\QueryException::class);
    }

    public function test_soft_delete_preserves_versions_and_force_delete_cascades(): void
    {
        $document = Document::factory()->create();
        DocumentVersion::factory()->create(['document_id' => $document->id, 'version_number' => 1]);

        $document->delete();

        $this->assertSoftDeleted('documents', ['id' => $document->id]);
        $this->assertDatabaseHas('document_versions', ['document_id' => $document->id]);

        $document->forceDelete();

        $this->assertDatabaseMissing('document_versions', ['document_id' => $document->id]);
    }

    public function test_favorite_is_unique_per_user_and_document(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();

        Favorite::create(['user_id' => $user->id, 'document_id' => $document->id]);

        $this->assertThrows(function () use ($user, $document) {
            Favorite::create(['user_id' => $user->id, 'document_id' => $document->id]);
        }, \Illuminate\Database\QueryException::class);
    }

    public function test_audit_log_is_polymorphic(): void
    {
        $document = Document::factory()->create();
        $user = User::factory()->create();

        $log = AuditLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
            'description' => 'Dokumen dibuat',
            'metadata' => ['title' => $document->title],
        ]);

        $this->assertInstanceOf(Document::class, $log->auditable);
        $this->assertEquals($document->id, $log->auditable->id);
        $this->assertEquals(['title' => $document->title], $log->metadata);
    }

    public function test_stage_supports_parent_child_hierarchy(): void
    {
        $parent = Stage::factory()->create();
        $child = Stage::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertEquals($child->id, $parent->children->first()->id);
    }

    public function test_soft_deleted_document_is_restorable(): void
    {
        $document = Document::factory()->create();
        $document->delete();

        $this->assertTrue(Document::withTrashed()->find($document->id)->exists);
        $this->assertNull(Document::find($document->id));

        $document->restore();

        $this->assertNotNull(Document::find($document->id));
    }

    public function test_document_status_enum_casts(): void
    {
        $document = Document::factory()->active()->create();

        $this->assertInstanceOf(DocumentStatus::class, $document->status);
        $this->assertEquals(DocumentStatus::Active, $document->status);
    }
}
