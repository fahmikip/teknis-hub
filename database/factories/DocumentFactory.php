<?php

namespace Database\Factories;

use App\Enums\AccessLevel;
use App\Enums\DocumentStatus;
use App\Models\Category;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        $year = fake()->numberBetween(2020, (int) date('Y'));

        return [
            'title' => fake()->sentence(5),
            'document_number' => fake()->optional()->numerify('###/###/DT/EK/' . $year),
            'document_type_id' => DocumentType::factory(),
            'category_id' => Category::factory(),
            'stage_id' => null,
            'year' => $year,
            'document_date' => fake()->date(),
            'status' => fake()->randomElement(DocumentStatus::values()),
            'access_level' => fake()->randomElement(AccessLevel::values()),
            'description' => fake()->paragraph(),
            'keywords' => fake()->words(4, true),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => DocumentStatus::Draft]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => DocumentStatus::Active]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => DocumentStatus::Archived]);
    }
}
