<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Keputusan',
            'Surat',
            'Pedoman',
            'Petunjuk Teknis',
            'Berita Acara',
            'Formulir',
            'Laporan',
            'Notulen',
            'Data',
            'Dokumentasi',
            'Lainnya',
        ];

        foreach ($types as $index => $name) {
            DocumentType::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}