<x-app-layout :title="'Aktivitas Sistem'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Aktivitas Sistem' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Aktivitas Sistem</h1>
            <p class="mt-1 text-sm text-ink-muted">Riwayat aktivitas pengguna di TeknisHub</p>
        </div>

        <form method="GET" action="{{ route('audit-logs.index') }}" class="card" aria-label="Filter aktivitas">
            <div class="card-body grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div class="relative sm:col-span-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                        <x-icon name="search" size="18" />
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari aktivitas..."
                           class="w-full rounded-md border-line bg-white pl-11 pr-4 py-2.5 text-sm text-ink placeholder:text-ink-light shadow-sm focus:border-brand focus:ring-brand">
                </div>
                <select name="action" class="select">
                    <option value="">Semua Aksi</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
                <select name="user_id" class="select">
                    <option value="">Semua Pengguna</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
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
                            <th class="px-5 py-3 font-medium">Waktu</th>
                            <th class="px-5 py-3 font-medium">Pengguna</th>
                            <th class="px-5 py-3 font-medium">Aksi</th>
                            <th class="px-5 py-3 font-medium">Deskripsi</th>
                            <th class="px-5 py-3 font-medium">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-app/60 transition-colors">
                                <td class="px-5 py-3 text-ink-muted whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="px-5 py-3">{{ $log->user?->name ?? 'Sistem' }}</td>
                                <td class="px-5 py-3">
                                    <x-badge color="neutral" :dot="false">{{ $log->action }}</x-badge>
                                </td>
                                <td class="px-5 py-3 text-ink max-w-md truncate">{{ $log->description }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16">
                                    <div class="flex flex-col items-center justify-center text-center gap-3">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-app border border-line text-ink-light">
                                            <x-icon name="activity" size="22" />
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-ink">Tidak ada aktivitas</p>
                                            <p class="mt-1 text-2xs text-ink-muted max-w-xs">Aktivitas pengguna akan muncul di sini.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="px-5 py-4 border-t border-line">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>