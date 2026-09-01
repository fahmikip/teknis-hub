<x-app-layout :title="'Kategori'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Kategori' => null]" />

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-ink">Kategori</h1>
                <p class="mt-1 text-sm text-ink-muted">Kelola kategori dokumen Divisi Teknis</p>
            </div>
            @can('create', App\Models\Category::class)
                <a href="{{ route('categories.create') }}" class="btn-primary shrink-0">
                    <x-icon name="plus" size="16" />
                    Tambah Kategori
                </a>
            @endcan
        </div>

        {{-- Filter & pencarian --}}
        <form method="GET" action="{{ route('categories.index') }}" class="card" aria-label="Filter kategori">
            <div class="card-body grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div class="relative sm:col-span-2">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                        <x-icon name="search" size="18" />
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kategori..."
                        class="w-full rounded-md border-line bg-white pl-11 pr-4 py-2.5 text-sm text-ink placeholder:text-ink-light shadow-sm focus:border-brand focus:ring-brand"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <select name="status" class="select flex-1">
                        <option value="">Semua Status</option>
                        <option value="1" @selected(request('status') === '1')>Aktif</option>
                        <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                    </select>
                    <button type="submit" class="btn-secondary shrink-0">
                        <x-icon name="filter" size="16" />
                    </button>
                </div>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-2xs uppercase tracking-wide text-ink-muted border-b border-line">
                            <th class="px-5 py-3 font-medium w-12">No</th>
                            <th class="px-5 py-3 font-medium">Nama</th>
                            <th class="px-5 py-3 font-medium">Slug</th>
                            <th class="px-5 py-3 font-medium">Jumlah Dokumen</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-app/60 transition-colors">
                                <td class="px-5 py-3 text-ink-muted">{{ $categories->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3 font-medium text-ink">{{ $category->name }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $category->slug }}</td>
                                <td class="px-5 py-3">{{ $category->documents_count }}</td>
                                <td class="px-5 py-3">
                                    @if ($category->is_active)
                                        <x-badge color="success">Aktif</x-badge>
                                    @else
                                        <x-badge color="neutral">Nonaktif</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $category)
                                            <a href="{{ route('categories.edit', $category) }}" class="btn-action">
                                                <x-icon name="edit" size="14" />
                                                Edit
                                            </a>
                                        @endcan
                                        @can('delete', $category)
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                                  onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-danger">
                                                    <x-icon name="trash" size="14" />
                                                    Hapus
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16">
                                    <div class="flex flex-col items-center justify-center text-center gap-3">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-app border border-line text-ink-light">
                                            <x-icon name="folder" size="22" />
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-ink">Belum ada kategori</p>
                                            <p class="mt-1 text-2xs text-ink-muted max-w-xs">Belum ada kategori yang tersimpan di TeknisHub.</p>
                                        </div>
                                        @can('create', App\Models\Category::class)
                                            <a href="{{ route('categories.create') }}" class="btn-primary">Tambah Kategori</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="px-5 py-4 border-t border-line">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
