<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function placeholder(string $module)
    {
        $titles = [
            'documents' => 'Modul Dokumen',
            'recent' => 'Dokumen Terbaru',
            'favorites' => 'Dokumen Favorit',
            'archived' => 'Dokumen Arsip',
            'categories' => 'Kategori Dokumen',
            'stages' => 'Tahapan',
            'document-types' => 'Jenis Dokumen',
            'audit-logs' => 'Aktivitas Sistem',
            'users' => 'Manajemen Pengguna',
            'roles' => 'Role & Permission',
            'settings' => 'Pengaturan',
        ];

        return view('pages.placeholder', [
            'title' => $titles[$module] ?? 'Modul',
            'module' => $module,
        ]);
    }
}
