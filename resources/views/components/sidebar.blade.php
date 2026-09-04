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
                ['route' => 'documents.index', 'label' => 'Semua Dokumen', 'icon' => 'files', 'ability' => ['viewAny', App\Models\Document::class]],
                ['route' => 'documents.recent', 'label' => 'Dokumen Terbaru', 'icon' => 'file-text', 'ability' => ['viewAny', App\Models\Document::class]],
                ['route' => 'favorites.index', 'label' => 'Favorit', 'icon' => 'star', 'ability' => ['viewAny', App\Models\Favorite::class]],
                ['route' => 'documents.archived', 'label' => 'Arsip', 'icon' => 'archive', 'ability' => ['viewAny', App\Models\Document::class]],
            ],
            'Referensi' => [
                ['route' => 'categories.index', 'label' => 'Kategori', 'icon' => 'folder', 'ability' => ['viewAny', App\Models\Category::class]],
                ['route' => 'stages.index', 'label' => 'Tahapan', 'icon' => 'layers', 'ability' => ['viewAny', App\Models\Stage::class]],
                ['route' => 'document-types.index', 'label' => 'Jenis Dokumen', 'icon' => 'tag', 'ability' => ['viewAny', App\Models\DocumentType::class]],
            ],
            'Sistem' => [
                ['route' => 'audit-logs.index', 'label' => 'Aktivitas', 'icon' => 'activity', 'ability' => ['viewAny', App\Models\AuditLog::class]],
                ['route' => 'notifications.index', 'label' => 'Notifikasi', 'icon' => 'bell', 'ability' => ['viewAny', App\Models\User::class]],
                ['route' => 'users.index', 'label' => 'Pengguna', 'icon' => 'users', 'ability' => ['viewAny', App\Models\User::class]],
                ['route' => 'roles.index', 'label' => 'Role & Permission', 'icon' => 'shield', 'ability' => ['viewAny', App\Models\Role::class]],
                ['route' => 'backups.index', 'label' => 'Backup', 'icon' => 'database', 'ability' => ['viewAny', App\Models\Setting::class]],
                ['route' => 'settings.index', 'label' => 'Pengaturan', 'icon' => 'settings', 'ability' => ['viewAny', App\Models\Setting::class]],
            ],
        ];
    @endphp

    @foreach ($groups as $group => $items)
        <div>
            <p class="px-3 pb-2 text-2xs font-semibold uppercase tracking-wider text-white/50">{{ $group }}</p>
            <ul class="space-y-0.5">
                @foreach ($items as $item)
                    @if (Route::has($item['route']) && (auth()->user()->can(...$item['ability'])))
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