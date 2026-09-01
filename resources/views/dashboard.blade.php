<x-app-layout
    :title="'Dashboard'"
>

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => null]" />

        {{-- KPI --}}
        <section aria-label="Ringkasan dokumen" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card card-body">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Total Dokumen</p>
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">{{ number_format($stats['totalDocuments']) }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-app border border-line text-ink-muted shrink-0">
                        <x-icon name="files" size="18" />
                    </span>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Dokumen Tahun {{ $currentYear }}</p>
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">{{ number_format($stats['currentYearDocuments']) }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-app border border-line text-ink-muted shrink-0">
                        <x-icon name="clock" size="18" />
                    </span>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Dokumen Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">{{ number_format($stats['activeDocuments']) }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-app border border-line text-ink-muted shrink-0">
                        <x-icon name="check" size="18" />
                    </span>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Dokumen Baru Bulan Ini</p>
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">{{ number_format($stats['newThisMonth']) }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-app border border-line text-ink-muted shrink-0">
                        <x-icon name="plus" size="18" />
                    </span>
                </div>
            </div>
        </section>

        {{-- Konten --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Dokumen terbaru --}}
            <section class="xl:col-span-2 card" aria-label="Dokumen terbaru">
                <div class="card-header">
                    <h3 class="card-title">Dokumen Terbaru</h3>
                    @if (Route::has('documents.index'))
                        <a href="{{ route('documents.index') }}" class="btn-link">
                            Lihat semua
                            <x-icon name="chevron-right" size="16" />
                        </a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-2xs uppercase tracking-wide text-ink-muted border-b border-line">
                                <th class="px-5 py-3 font-medium">No</th>
                                <th class="px-5 py-3 font-medium">Judul</th>
                                <th class="px-5 py-3 font-medium">Kategori</th>
                                <th class="px-5 py-3 font-medium">Tahun</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 font-medium">Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($recentDocuments as $i => $doc)
                                <tr class="hover:bg-app/60 transition-colors">
                                    <td class="px-5 py-3 text-ink-muted">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <a href="{{ route('documents.show', $doc) }}" class="font-medium text-ink hover:text-brand hover:underline line-clamp-2">{{ $doc->title }}</a>
                                    </td>
                                    <td class="px-5 py-3 text-ink-muted">{{ $doc->category?->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-ink-muted">{{ $doc->year }}</td>
                                    <td class="px-5 py-3">
                                        @if ($doc->status === App\Enums\DocumentStatus::Active->value)
                                            <x-badge color="success">Aktif</x-badge>
                                        @elseif ($doc->status === App\Enums\DocumentStatus::Archived->value)
                                            <x-badge color="neutral">Arsip</x-badge>
                                        @else
                                            <x-badge color="warning">Draft</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-ink-muted whitespace-nowrap">{{ $doc->updated_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10">
                                        <div class="flex flex-col items-center justify-center text-center gap-2">
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-app border border-line text-ink-light">
                                                <x-icon name="inbox" size="20" />
                                            </span>
                                            <p class="text-sm font-medium text-ink">Belum ada dokumen</p>
                                            <p class="text-2xs text-ink-muted max-w-xs">Dokumen akan tampil di sini setelah diunggah.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Aktivitas terbaru --}}
            <section class="card" aria-label="Aktivitas terbaru">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Terbaru</h3>
                </div>
                <div class="card-body">
                    @forelse ($recentActivity as $activity)
                        <div class="flex items-start gap-3 py-2.5 border-b border-line last:border-0">
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-app border border-line text-ink-light shrink-0">
                                <x-icon name="activity" size="16" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs text-ink line-clamp-2">{{ $activity->description }}</p>
                                <p class="mt-0.5 text-2xs text-ink-muted">{{ $activity->user?->name ?? 'Sistem' }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center text-center gap-2 py-8">
                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-app border border-line text-ink-light">
                                <x-icon name="activity" size="20" />
                            </span>
                            <p class="text-sm font-medium text-ink">Belum ada aktivitas</p>
                            <p class="text-2xs text-ink-muted max-w-xs">Aktivitas pengguna akan tercatat otomatis di sini.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Statistik status & favorit --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Status dokumen --}}
            <section class="card" aria-label="Statistik status dokumen">
                <div class="card-header">
                    <h3 class="card-title">Status Dokumen</h3>
                </div>
                <div class="card-body space-y-3">
                    @forelse ($statusStats as $label => $total)
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm text-ink">{{ $label }}</span>
                            <span class="text-sm font-semibold text-ink">{{ number_format($total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-ink-muted">Belum ada data.</p>
                    @endforelse
                </div>
            </section>

            {{-- Favorit --}}
            <section class="xl:col-span-2 card" aria-label="Dokumen favorit">
                <div class="card-header">
                    <h3 class="card-title">Dokumen Favorit Saya</h3>
                    @if (Route::has('favorites.index'))
                        <a href="{{ route('favorites.index') }}" class="btn-link">
                            Lihat semua
                            <x-icon name="chevron-right" size="16" />
                        </a>
                    @endif
                </div>
                <div class="card-body divide-y divide-line">
                    @forelse ($favoriteDocuments as $fav)
                        <a href="{{ route('documents.show', $fav->document) }}" class="flex items-center justify-between gap-3 py-3 hover:bg-app/60 -mx-2 px-2 rounded-md transition-colors">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-ink line-clamp-1">{{ $fav->document->title }}</p>
                                <p class="mt-0.5 text-2xs text-ink-muted">{{ $fav->document->year }} &middot; {{ $fav->document->category?->name ?? 'Tanpa kategori' }}</p>
                            </div>
                            <x-icon name="star" size="16" class="text-gold shrink-0" />
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center text-center gap-2 py-8">
                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-app border border-line text-ink-light">
                                <x-icon name="star" size="20" />
                            </span>
                            <p class="text-sm font-medium text-ink">Belum ada favorit</p>
                            <p class="text-2xs text-ink-muted max-w-xs">Tandai dokumen penting dari halaman daftar dokumen.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

</x-app-layout>