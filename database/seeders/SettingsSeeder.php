<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'TeknisHub', 'type' => 'string', 'description' => 'Nama aplikasi'],
            ['key' => 'institution_name', 'value' => 'KPU Kabupaten', 'type' => 'string', 'description' => 'Nama instansi'],
            ['key' => 'institution_region', 'value' => '', 'type' => 'string', 'description' => 'Nama Kabupaten/Kota'],
            ['key' => 'footer_text', 'value' => 'Sistem Manajemen Dokumen & Informasi Divisi Teknis', 'type' => 'string', 'description' => 'Teks footer'],
            ['key' => 'max_upload_size', 'value' => '20480', 'type' => 'integer', 'description' => 'Ukuran upload maksimal dalam KB (default 20MB)'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}