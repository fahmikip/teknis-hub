@extends('layouts.app')

@section('content')
    <x-slot name="breadcrumb">
        <x-breadcrumb>
            <x-breadcrumb-item href="{{ route('dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item>Backup</x-breadcrumb-item>
        </x-breadcrumb>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <div>
            <h1 class="text-xl font-bold text-ink">Backup</h1>
            <p class="mt-1 text-sm text-ink-muted">Kelola backup database dan file dokumen.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-surface border border-line rounded-lg p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-blue-100 text-blue-700">
                        <x-icon name="database" size="20" />
                    </span>
                    <div>
                        <h2 class="text-sm font-semibold text-ink">Database</h2>
                        <p class="text-xs text-ink-muted">Backup seluruh data aplikasi</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('backups.database') }}">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-md bg-brand text-white text-sm font-medium hover:bg-brand-dark transition-colors">
                        <x-icon name="download" size="16" />
                        Buat Backup Database
                    </button>
                </form>
            </div>

            <div class="bg-surface border border-line rounded-lg p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-green-100 text-green-700">
                        <x-icon name="files" size="20" />
                    </span>
                    <div>
                        <h2 class="text-sm font-semibold text-ink">File Dokumen</h2>
                        <p class="text-xs text-ink-muted">Backup semua file PDF</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('backups.files') }}">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition-colors">
                        <x-icon name="download" size="16" />
                        Buat Backup File
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-surface border border-line rounded-lg">
            <div class="px-5 py-3 border-b border-line">
                <h2 class="text-sm font-semibold text-ink">Riwayat Backup Database</h2>
            </div>
            @if ($dbBackups->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-ink-muted">Belum ada backup database.</div>
            @else
                <div class="divide-y divide-line">
                    @foreach ($dbBackups as $backup)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <x-icon name="file-text" size="16" class="text-ink-muted" />
                                <div>
                                    <p class="text-sm text-ink">{{ $backup['name'] }}</p>
                                    <p class="text-xs text-ink-muted">{{ $backup['date_formatted'] }} &middot; {{ $backup['size_formatted'] }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('backups.destroy', $backup['name']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-ink-muted hover:text-danger" onclick="return confirm('Hapus backup ini?')">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-surface border border-line rounded-lg">
            <div class="px-5 py-3 border-b border-line">
                <h2 class="text-sm font-semibold text-ink">Riwayat Backup File</h2>
            </div>
            @if ($fileBackups->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-ink-muted">Belum ada backup file.</div>
            @else
                <div class="divide-y divide-line">
                    @foreach ($fileBackups as $backup)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <x-icon name="archive" size="16" class="text-ink-muted" />
                                <div>
                                    <p class="text-sm text-ink">{{ $backup['name'] }}</p>
                                    <p class="text-xs text-ink-muted">{{ $backup['date_formatted'] }} &middot; {{ $backup['size_formatted'] }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('backups.destroy', $backup['name']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-ink-muted hover:text-danger" onclick="return confirm('Hapus backup ini?')">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
