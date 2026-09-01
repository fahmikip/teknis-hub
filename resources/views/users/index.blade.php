<x-app-layout :title="'Pengguna'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Pengguna' => null]" />

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-ink">Pengguna</h1>
                <p class="mt-1 text-sm text-ink-muted">Kelola akun pengguna dan peran mereka</p>
            </div>
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="btn-primary shrink-0">
                    <x-icon name="plus" size="16" />
                    Tambah Pengguna
                </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('users.index') }}" class="card" aria-label="Filter pengguna">
            <div class="card-body grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div class="relative sm:col-span-2">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                        <x-icon name="search" size="18" />
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau username..."
                           class="w-full rounded-md border-line bg-white pl-11 pr-4 py-2.5 text-sm text-ink placeholder:text-ink-light shadow-sm focus:border-brand focus:ring-brand">
                </div>
                <select name="role_id" class="select">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="select">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                </select>
                <button type="submit" class="btn-secondary shrink-0">
                    <x-icon name="filter" size="16" />
                    Filter
                </button>
            </div>
        </form>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-2xs uppercase tracking-wide text-ink-muted border-b border-line">
                            <th class="px-5 py-3 font-medium w-12">No</th>
                            <th class="px-5 py-3 font-medium">Pengguna</th>
                            <th class="px-5 py-3 font-medium">Role</th>
                            <th class="px-5 py-3 font-medium">Dokumen</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($users as $user)
                            <tr class="hover:bg-app/60 transition-colors">
                                <td class="px-5 py-3 text-ink-muted">{{ $users->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-ink">{{ $user->name }}</p>
                                    <p class="text-2xs text-ink-muted">{{ $user->email }} @if ($user->username) &middot;{{ $user->username }} @endif</p>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($user->roles as $role)
                                            <x-badge color="info" :dot="false">{{ $role->name }}</x-badge>
                                        @empty
                                            <span class="text-ink-muted">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-3">{{ $user->documents_created_count }}</td>
                                <td class="px-5 py-3">
                                    @if ($user->is_active)
                                        <x-badge color="success">Aktif</x-badge>
                                    @else
                                        <x-badge color="neutral">Nonaktif</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $user)
                                            <a href="{{ route('users.edit', $user) }}" class="btn-action">
                                                <x-icon name="edit" size="14" />
                                                Edit
                                            </a>
                                        @endcan
                                        @can('delete', $user)
                                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                                  onsubmit="return confirm('Nonaktifkan akun pengguna ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-danger">
                                                    <x-icon name="trash" size="14" />
                                                    Nonaktifkan
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
                                            <x-icon name="users" size="22" />
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-ink">Belum ada pengguna</p>
                                            <p class="mt-1 text-2xs text-ink-muted max-w-xs">Tambahkan pengguna untuk memberikan akses ke TeknisHub.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-5 py-4 border-t border-line">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>