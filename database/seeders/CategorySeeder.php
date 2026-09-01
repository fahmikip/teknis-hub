<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Regulasi',
            'Keputusan',
            'Surat Dinas',
            'Pedoman',
            'Petunjuk Teknis',
            'Formulir',
            'Berita Acara',
            'Data Pemilih',
            'Pencalonan',
            'Kampanye',
            'Pemungutan dan Penghitungan Suara',
            'Rekapitulasi',
            'Penetapan Hasil',
            'Dokumentasi Kegiatan',
            'Lainnya',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
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