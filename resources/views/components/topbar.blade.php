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

        <button
            type="button"
            class="relative inline-flex items-center justify-center h-10 w-10 rounded-md text-ink-muted hover:bg-app hover:text-ink transition-colors"
            aria-label="Notifikasi"
        >
            <x-icon name="bell" size="20" />
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-brand ring-2 ring-white" aria-hidden="true"></span>
        </button>

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
