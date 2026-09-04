<header class="sticky top-0 z-20 h-16 bg-surface border-b border-line flex items-center gap-3 px-4 sm:px-6">
    <button
        type="button"
        class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-md text-ink-muted hover:bg-app hover:text-ink transition-colors"
        @click="$dispatch('toggle-sidebar')"
        aria-label="Buka navigasi"
    >
        <x-icon name="menu" size="20" />
    </button>

    <div class="hidden lg:block min-w-0 flex-1">
        {{ $breadcrumb ?? '' }}
    </div>

    <div class="flex-1 lg:hidden"></div>

    <div class="flex items-center gap-1 sm:gap-2">
        <div class="hidden md:block relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-light">
                <x-icon name="search" size="16" />
            </span>
            <input
                type="search"
                placeholder="Cari dokumen, nomor, kata kunci..."
                class="w-56 lg:w-72 rounded-md border-line bg-app pl-9 pr-3 py-1.5 text-sm text-ink placeholder:text-ink-light focus:border-brand focus:ring-brand"
            >
        </div>
        <button
            type="button"
            class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-md text-ink-muted hover:bg-app transition-colors"
            aria-label="Cari"
        >
            <x-icon name="search" size="20" />
        </button>

        @php
            $unreadCount = auth()->user()->unreadNotifications()->count();
            $recentNotifications = auth()->user()->notifications()->latest()->limit(5)->get();
        @endphp
        <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
            <button
                type="button"
                class="relative inline-flex items-center justify-center h-10 w-10 rounded-md text-ink-muted hover:bg-app hover:text-ink transition-colors"
                @click="notifOpen = !notifOpen"
                aria-label="Notifikasi"
            >
                <x-icon name="bell" size="20" />
                @if ($unreadCount > 0)
                    <span class="absolute top-1.5 right-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-brand text-white text-2xs font-semibold px-1" aria-hidden="true">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </button>

            <div
                x-show="notifOpen"
                x-transition
                x-cloak
                class="absolute right-0 mt-2 w-80 origin-top-right rounded-md bg-surface border border-line shadow-card z-30"
                role="menu"
                @keydown.escape.window="notifOpen = false"
            >
                <div class="px-4 py-2.5 border-b border-line flex items-center justify-between">
                    <p class="text-sm font-semibold text-ink">Notifikasi</p>
                    @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-brand hover:underline">Tandai semua dibaca</button>
                        </form>
                    @endif
                </div>
                <div class="max-h-80 overflow-y-auto">
                    @if ($recentNotifications->isEmpty())
                        <div class="px-4 py-8 text-center">
                            <p class="text-sm text-ink-muted">Tidak ada notifikasi</p>
                        </div>
                    @else
                        @foreach ($recentNotifications as $notif)
                            @php $d = $notif->data; @endphp
                            <a href="{{ $d['document_id'] ?? '#' }}"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-app transition-colors {{ $notif->read_at === null ? 'bg-red-50/30' : '' }}">
                                <span class="mt-0.5 shrink-0">
                                    @if (($d['action'] ?? '') === 'created')
                                        <x-icon name="plus" size="16" class="text-green-600" />
                                    @elseif (($d['action'] ?? '') === 'updated')
                                        <x-icon name="edit" size="16" class="text-blue-600" />
                                    @elseif (($d['action'] ?? '') === 'version_uploaded')
                                        <x-icon name="upload" size="16" class="text-amber-600" />
                                    @else
                                        <x-icon name="bell" size="16" class="text-ink-muted" />
                                    @endif
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-ink leading-snug line-clamp-2">{{ $d['message'] ?? '' }}</p>
                                    <p class="mt-1 text-2xs text-ink-muted">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                @if ($notif->read_at === null)
                                    <span class="mt-1 shrink-0 w-2 h-2 rounded-full bg-brand"></span>
                                @endif
                            </a>
                        @endforeach
                    @endif
                </div>
                <div class="border-t border-line px-4 py-2.5">
                    <a href="{{ route('notifications.index') }}" class="text-xs text-brand hover:underline">Lihat semua notifikasi</a>
                </div>
            </div>
        </div>

        <div class="ml-1 sm:ml-2 hidden sm:block h-6 w-px bg-line"></div>

        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button
                type="button"
                class="flex items-center gap-2.5 ml-1 sm:ml-2 rounded-md px-1.5 py-1.5 hover:bg-app transition-colors"
                @click="open = !open"
                aria-haspopup="menu"
                :aria-expanded="open"
                aria-label="Menu pengguna"
            >
                <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-brand text-white text-sm font-semibold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </span>
                <span class="hidden xl:block text-left leading-tight">
                    <span class="block text-sm font-medium text-ink">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                    <span class="block text-2xs text-ink-muted">Administrator</span>
                </span>
                <x-icon name="chevron-down" size="16" class="hidden xl:block text-ink-light" />
            </button>

            <div
                x-show="open"
                x-transition
                x-cloak
                class="absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-surface border border-line shadow-card py-1 z-30"
                role="menu"
                @keydown.escape.window="open = false"
            >
                <div class="px-4 py-2.5 border-b border-line">
                    <p class="text-sm font-medium text-ink truncate">{{ auth()->user()->name }}</p>
                    <p class="text-2xs text-ink-muted truncate">{{ auth()->user()->email }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-ink hover:bg-app transition-colors" role="menuitem">
                        <x-icon name="user" size="16" class="text-ink-muted" />
                        Profil
                    </a>
                </div>
                <div class="border-t border-line py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors" role="menuitem">
                            <x-icon name="log-out" size="16" />
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
