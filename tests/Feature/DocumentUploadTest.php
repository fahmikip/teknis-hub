<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('local');
    }

    protected function makeUser(string $role = 'ADMIN'): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(\App\Models\Role::where('name', $role)->firstOrFail());
        return $user;
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Dokumen Upload',
            'document_type_id' => DocumentType::factory()->create()->id,
            'category_id' => Category::factory()->create()->id,
            'year' => 2026,
            'status' => 'active',
            'access_level' => 'internal',
        ], $overrides);
    }

    public function test_valid_pdf_is_accepted(): void
    {
        $user = $this->makeUser();

        $file = UploadedFile::fake()->createWithContent('dokumen.pdf', '%PDF-1.4 fake pdf content');

        $this->actingAs($user)
            ->post(route('documents.store'), $this->payload(['file' => $file]))
            ->assertRedirect();

        $document = Document::where('title', 'Dokumen Upload')->firstOrFail();
        $version = $document->versions()->firstOrFail();

        $this->assertDatabaseHas('document_versions', [
            'document_id' => $document->id,
            'version_number' => 1,
            'original_filename' => 'dokumen.pdf',
        ]);

        Storage::disk('local')->assertExists($version->file_path);
        $this->assertStringStartsWith('documents/', $version->file_path);
    }

    public function test_non_pdf_is_rejected(): void
    {
        $user = $this->makeUser();

        $file = UploadedFile::fake()->createWithContent('dokumen.txt', 'this is not a pdf');

        $this->from(route('documents.create'))
            ->actingAs($user)
            ->post(route('documents.store'), $this->payload(['file' => $file]))
            ->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('documents', ['title' => 'Dokumen Upload']);
    }

    public function test_oversized_file_is_rejected(): void
    {
        $user = $this->makeUser();

        config(['documents.max_upload_size_kb' => 512]);

        $file = UploadedFile::fake()->createWithContent('besar.pdf', str_repeat('%PDF-1.4 ', 70000));

        $this->from(route('documents.create'))
            ->actingAs($user)
            ->post(route('documents.store'), $this->payload(['file' => $file]))
            ->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('documents', ['title' => 'Dokumen Upload']);
    }

    public function test_invalid_category_is_rejected(): void
    {
        $user = $this->makeUser();

        $this->from(route('documents.create'))
            ->actingAs($user)
            ->post(route('documents.store'), $this->payload([
                'category_id' => 999999,
                'file' => UploadedFile::fake()->createWithContent('a.pdf', '%PDF-1.4 data'),
            ]))
            ->assertSessionHasErrors('category_id');
    }

    public function test_invalid_document_type_is_rejected(): void
    {
        $user = $this->makeUser();

        $this->from(route('documents.create'))
            ->actingAs($user)
            ->post(route('documents.store'), $this->payload([
                'document_type_id' => 999999,
                'file' => UploadedFile::fake()->createWithContent('a.pdf', '%PDF-1.4 data'),
            ]))
            ->assertSessionHasErrors('document_type_id');
    }

    public function test_year_must_be_valid_integer(): void
    {
        $user = $this->makeUser();

        $this->from(route('documents.create'))
            ->actingAs($user)
            ->post(route('documents.store'), $this->payload([
                'year' => -999,
                'file' => UploadedFile::fake()->createWithContent('a.pdf', '%PDF-1.4 data'),
            ]))
            ->assertSessionHasErrors('year');
    }

    public function test_title_is_required(): void
    {
        $user = $this->makeUser();

        $this->from(route('documents.create'))
            ->actingAs($user)
            ->post(route('documents.store'), $this->payload([
                'title' => '',
                'file' => UploadedFile::fake()->createWithContent('a.pdf', '%PDF-1.4 data'),
            ]))
            ->assertSessionHasErrors('title');
    }

    public function test_file_is_required_on_create(): void
    {
        $user = $this->makeUser();

        $this->from(route('documents.create'))
            ->actingAs($user)
            ->post(route('documents.store'), $this->payload())
            ->assertSessionHasErrors('file');
    }
}
