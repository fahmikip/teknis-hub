<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'SUPER ADMIN' => Permission::pluck('name')->all(),
            'ADMIN' => [
                'view_dashboard',
                'view_documents',
                'create_documents',
                'edit_documents',
                'archive_documents',
                'restore_documents',
                'download_documents',
                'preview_documents',
                'manage_document_versions',
                'manage_categories',
                'manage_stages',
                'manage_document_types',
                'view_audit_logs',
                'view_users',
                'create_users',
                'edit_users',
                'deactivate_users',
            ],
            'OPERATOR' => [
                'view_dashboard',
                'view_documents',
                'create_documents',
                'edit_documents',
                'download_documents',
                'preview_documents',
                'manage_document_versions',
            ],
            'VIEWER' => [
                'view_dashboard',
                'view_documents',
                'download_documents',
                'preview_documents',
            ],
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                [
                    'slug' => Str::slug($roleName),
                    'label' => Str::title($roleName),
                    'description' => 'Peran ' . Str::lower($roleName) . ' pada sistem TeknisHub.',
                ]
            );

            $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        $this->createAdminUser();
    }

    protected function createAdminUser(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@teknishub.test');
        $password = env('ADMIN_PASSWORD', 'password');
        $name = env('ADMIN_NAME', 'Administrator');

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'username' => Str::slug($name, ''),
                'password' => $password,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $superAdmin = Role::where('name', 'SUPER ADMIN')->first();
        if ($superAdmin) {
            $admin->roles()->syncWithoutDetaching([$superAdmin->id]);
        }
    }
}