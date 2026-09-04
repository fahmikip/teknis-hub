@extends('layouts.app')

@section('content')
    <x-slot name="breadcrumb">
        <x-breadcrumb>
            <x-breadcrumb-item href="{{ route('dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item>Notifikasi</x-breadcrumb-item>
        </x-breadcrumb>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-ink">Notifikasi</h1>
                <p class="mt-1 text-sm text-ink-muted">Daftar notifikasi aktivitas dokumen.</p>
            </div>
            <div class="flex items-center gap-2">
                @if ($notifications->total() > 0)
                    <form method="POST" action="{{ route('notifications.clear-all') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-ink-muted hover:text-danger transition-colors"
                                onclick="return confirm('Hapus semua notifikasi?')">
                            Hapus Semua
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if ($notifications->total() > 0)
            <div class="bg-surface border border-line rounded-lg divide-y divide-line">
                @foreach ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = $notification->read_at === null;
                    @endphp
                    <div class="flex items-start gap-4 px-5 py-4 {{ $isUnread ? 'bg-red-50/30' : '' }}">
                        <div class="mt-0.5 shrink-0">
                            @if ($data['action'] === 'created')
                                <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-green-100 text-green-700">
                                    <x-icon name="plus" size="18" />
                                </span>
                            @elseif ($data['action'] === 'updated')
                                <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-blue-100 text-blue-700">
                                    <x-icon name="edit" size="18" />
                                </span>
                            @elseif ($data['action'] === 'version_uploaded')
                                <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-amber-100 text-amber-700">
                                    <x-icon name="upload" size="18" />
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-gray-100 text-gray-700">
                                    <x-icon name="bell" size="18" />
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-ink {{ $isUnread ? 'font-medium' : '' }}">
                                {{ $data['message'] }}
                            </p>
                            <p class="mt-1 text-xs text-ink-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            @if ($data['document_id'] ?? false)
                                <a href="{{ route('documents.show', $data['document_id']) }}"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-md text-ink-muted hover:bg-app hover:text-ink transition-colors"
                                   title="Lihat dokumen">
                                    <x-icon name="eye" size="16" />
                                </a>
                            @endif
                            @if ($isUnread)
                                <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-ink-muted hover:bg-app hover:text-ink transition-colors"
                                            title="Tandai sudah dibaca">
                                        <x-icon name="check" size="16" />
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md text-ink-muted hover:bg-red-50 hover:text-danger transition-colors"
                                        title="Hapus">
                                    <x-icon name="trash" size="16" />
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="bg-surface border border-line rounded-lg px-6 py-12 text-center">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-app text-ink-muted mb-3">
                    <x-icon name="bell" size="24" />
                </div>
                <p class="text-sm text-ink-muted">Tidak ada notifikasi.</p>
            </div>
        @endif
    </div>
@endsection
