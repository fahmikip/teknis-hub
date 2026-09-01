<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disk Penyimpanan Dokumen
    |--------------------------------------------------------------------------
    |
    | Dokumen TeknisHub bersifat privat secara default. Disk "local" pada
    | Laravel menunjuk ke storage/app/private sehingga tidak dapat diakses
    | langsung melalui URL publik.
    |
    */

    'disk' => env('DOCUMENT_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Maksimum Ukuran Upload (Kilobyte)
    |--------------------------------------------------------------------------
    |
    | Default 20 MB (20480 KB) agar selaras dengan pengaturan server
    | (upload_max_filesize) bila tidak disesuaikan.
    |
    */

    'max_upload_size_kb' => env('DOCUMENT_MAX_UPLOAD_KB', 20480),

    /*
    |--------------------------------------------------------------------------
    | Format & MIME yang Diizinkan
    |--------------------------------------------------------------------------
    |
    | PDF adalah format utama. Daftar extension dan MIME digunakan untuk
    | validasi upload tambahan di luar pemeriksaan bawaan Laravel.
    |
    */

    'allowed_extensions' => ['pdf'],
    'allowed_mimetypes' => ['application/pdf'],
];
