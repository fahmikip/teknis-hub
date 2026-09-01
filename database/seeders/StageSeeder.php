<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            'pemilu' => [
                'Pemutakhiran Data',
                'Pencalonan',
                'Kampanye',
                'Masa Tenang',
                'Pemungutan Suara',
                'Penghitungan Suara',
                'Rekapitulasi',
                'Penetapan',
            ],
            'pilkada' => [
                'Pemutakhiran Data',
                'Pencalonan',
                'Kampanye',
                'Pemungutan Suara',
                'Rekapitulasi',
                'Penetapan',
            ],
            'general' => [
                'Persiapan',
                'Pelaksanaan',
                'Evaluasi',
                'Penetapan',
            ],
        ];

        foreach ($stages as $electionType => $names) {
            foreach ($names as $index => $name) {
                Stage::updateOrCreate(
                    ['slug' => Str::slug($name), 'election_type' => $electionType],
                    [
                        'name' => $name,
                        'description' => null,
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}