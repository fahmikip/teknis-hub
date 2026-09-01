<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'Dashboard' => ['view_dashboard'],
            'Dokumen' => [
                'view_documents',
                'create_documents',
                'edit_documents',
                'archive_documents',
                'restore_documents',
                'download_documents',
                'preview_documents',
                'manage_document_versions',
            ],
            'Referensi' => [
                'manage_categories',
                'manage_stages',
                'manage_document_types',
            ],
            'Pengguna' => [
                'manage_users',
                'view_users',
                'create_users',
                'edit_users',
                'deactivate_users',
            ],
            'Sistem' => [
                'view_audit_logs',
                'manage_roles',
                'manage_permissions',
                'manage_settings',
            ],
        ];

        foreach ($groups as $group => $names) {
            foreach ($names as $name) {
                Permission::updateOrCreate(
                    ['name' => $name],
                    [
                        'slug' => $name,
                        'label' => Str::headline(str_replace('_', ' ', $name)),
                        'group' => $group,
                        'description' => 'Hak akses untuk ' . Str::lower(Str::headline($name)),
                    ]
                );
            }
        }
    }
}