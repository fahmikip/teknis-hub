<x-app-layout :title="'Role &amp; Permission'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Role &amp; Permission' => null]" />

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-ink">Role &amp; Permission</h1>
                <p class="mt-1 text-sm text-ink-muted">Kelola peran dan hak akses pengguna</p>
            </div>
            @can('create', App\Models\Role::class)
                <a href="{{ route('roles.create') }}" class="btn-primary shrink-0">
                    <x-icon name="plus" size="16" />
                    Tambah Role
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('roles.index') }}" class="card" aria-label="Filter role">
            <div class="card-body">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                            <x-icon name="search" size="18" />
                        </span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari role..."
                               class="w-full rounded-md border-line bg-white pl-11 pr-4 py-2.5 text-sm text-ink placeholder:text-ink-light shadow-sm focus:border-brand focus:ring-brand">
                    </div>
                    <button type="submit" class="btn-secondary shrink-0">
                        <x-icon name="filter" size="16" />
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-2xs uppercase tracking-wide text-ink-muted border-b border-line">
                            <th class="px-5 py-3 font-medium w-12">No</th>
                            <th class="px-5 py-3 font-medium">Role</th>
                            <th class="px-5 py-3 font-medium">Deskripsi</th>
                            <th class="px-5 py-3 font-medium">Pengguna</th>
                            <th class="px-5 py-3 font-medium">Permission</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($roles as $role)
                            <tr class="hover:bg-app/60 transition-colors">
                                <td class="px-5 py-3 text-ink-muted">{{ $roles->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3 font-medium text-ink">{{ $role->name }}</td>
                                <td class="px-5 py-3 text-ink-muted max-w-xs truncate">{{ $role->description ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $role->users_count }}</td>
                                <td class="px-5 py-3">{{ $role->permissions_count }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $role)
                                            <a href="{{ route('roles.edit', $role) }}" class="btn-action">
                                                <x-icon name="edit" size="14" />
                                                Edit
                                            </a>
                                        @endcan
                                        @can('delete', $role)
                                            <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                                  onsubmit="return confirm('Hapus role ini?')" class="inline">
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
                                            <x-icon name="shield" size="22" />
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-ink">Belum ada role</p>
                                            <p class="mt-1 text-2xs text-ink-muted max-w-xs">Tambahkan role untuk mengatur hak akses.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="px-5 py-4 border-t border-line">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>