<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — {{ $title ?? '' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-app">
    <div class="min-h-full flex">
        {{-- Panel branding --}}
        <div class="hidden lg:flex lg:w-1/2 bg-ink text-white flex-col justify-between p-12">
            <x-application-logo markRed="true" />

            <div class="max-w-md space-y-6">
                <span class="inline-flex h-12 w-px bg-gold"></span>
                <h2 class="text-2xl font-semibold leading-snug">
                    Pusat Dokumen dan Informasi Divisi Teknis
                </h2>
                <p class="text-white/60 leading-relaxed">
                    Mengelola, menyimpan, dan mengarsipkan dokumen resmi secara
                    terpusat, aman, dan mudah ditemukan.
                </p>
            </div>

            <p class="text-2xs text-white/40">&copy; {{ date('Y') }} TeknisHub — Divisi Teknis</p>
        </div>

        {{-- Panel form --}}
        <div class="flex-1 flex flex-col items-center justify-center px-4 py-10">
            <div class="w-full max-w-sm">
                <div class="lg:hidden mb-8 flex justify-center">
                    <a href="/" class="inline-flex items-center gap-2 text-ink">
                        <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-brand text-white">
                            <x-icon name="layers" size="20" />
                        </span>
                        <span class="leading-tight">
                            <span class="block text-base font-semibold">TeknisHub</span>
                            <span class="block text-2xs uppercase tracking-wider text-ink-muted">Divisi Teknis</span>
                        </span>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
