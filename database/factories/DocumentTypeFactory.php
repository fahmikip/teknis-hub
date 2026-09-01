<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentType>
 */
class DocumentTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Keputusan', 'Surat', 'Pedoman', 'Petunjuk Teknis', 'Berita Acara',
            'Formulir', 'Laporan', 'Notulen', 'Data', 'Dokumentasi',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
