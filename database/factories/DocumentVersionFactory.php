<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentVersion>
 */
class DocumentVersionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'version_number' => fake()->numberBetween(1, 10),
            'file_path' => 'documents/' . fake()->uuid() . '.pdf',
            'original_filename' => fake()->slug(3) . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1024, 10485760),
            'checksum' => fake()->sha256(),
            'notes' => fake()->optional()->sentence(),
            'uploaded_by' => User::factory(),
        ];
    }
}
