@props(['collapsed' => false])

<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6" aria-label="Navigasi utama">
    <ul class="space-y-1">
        <li>
            <a href="{{ route('dashboard') }}"
               @class(['nav-item', 'nav-item-active' => request()->routeIs('dashboard')])>
                <x-icon name="dashboard" size="18" class="{{ request()->routeIs('dashboard') ? 'text-gold' : 'text-white/70' }}" />
                <span>Dashboard</span>
            </a>
        </li>
    </ul>

    @php
        $groups = [
            'Dokumen' => [
                ['route' => 'documents.index', 'label' => 'Semua Dokumen', 'icon' => 'files'],
                ['route' => 'documents.recent', 'label' => 'Dokumen Terbaru', 'icon' => 'file-text'],
                ['route' => 'favorites.index', 'label' => 'Favorit', 'icon' => 'star'],
                ['route' => 'documents.archived', 'label' => 'Arsip', 'icon' => 'archive'],
            ],
            'Referensi' => [
                ['route' => 'categories.index', 'label' => 'Kategori', 'icon' => 'folder'],
                ['route' => 'stages.index', 'label' => 'Tahapan', 'icon' => 'layers'],
                ['route' => 'document-types.index', 'label' => 'Jenis Dokumen', 'icon' => 'tag'],
            ],
            'Sistem' => [
                ['route' => 'audit-logs.index', 'label' => 'Aktivitas', 'icon' => 'activity'],
                ['route' => 'users.index', 'label' => 'Pengguna', 'icon' => 'users'],
                ['route' => 'roles.index', 'label' => 'Role & Permission', 'icon' => 'shield'],
                ['route' => 'settings.index', 'label' => 'Pengaturan', 'icon' => 'settings'],
            ],
        ];
    @endphp

    @foreach ($groups as $group => $items)
        <div>
            <p class="px-3 pb-2 text-2xs font-semibold uppercase tracking-wider text-white/50">{{ $group }}</p>
            <ul class="space-y-0.5">
                @foreach ($items as $item)
                    @if (Route::has($item['route']))
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @class(['nav-item', 'nav-item-active' => request()->routeIs($item['route'], $item['route'] . '.*')])>
                                <x-icon name="{{ $item['icon'] }}" size="18" class="{{ request()->routeIs($item['route'], $item['route'] . '.*') ? 'text-gold' : 'text-white/70' }}" />
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>
