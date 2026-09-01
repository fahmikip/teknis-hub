<?php

namespace Database\Factories;

use App\Enums\ElectionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stage>
 */
class StageFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Pemutakhiran Data', 'Pencalonan', 'Kampanye', 'Masa Tenang',
            'Pemungutan Suara', 'Penghitungan Suara', 'Rekapitulasi', 'Penetapan',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'election_type' => fake()->randomElement(ElectionType::values()),
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
