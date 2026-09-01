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
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">0</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-app border border-line text-ink-muted shrink-0">
                        <x-icon name="files" size="18" />
                    </span>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Dokumen Tahun Berjalan</p>
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">{{ date('Y') }}</p>
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
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">0</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-app border border-line text-ink-muted shrink-0">
                        <x-icon name="check" size="18" />
                    </span>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Dokumen Baru</p>
                        <p class="mt-1 text-2xl font-semibold text-ink leading-none">0</p>
                    </div>
                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-app border border-line text-ink-muted shrink-0">
                        <x-icon name="plus" size="18" />
                    </span>
                </div>
            </div>
        </section>

        {{-- Pencarian --}}
        <section class="card" aria-label="Pencarian dokumen">
            <div class="card-body">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                            <x-icon name="search" size="20" />
                        </span>
                        <input
                            type="search"
                            placeholder="Cari dokumen berdasarkan judul, nomor, kategori, tahun..."
                            class="w-full rounded-md border-line bg-white pl-11 pr-4 py-3 text-sm text-ink placeholder:text-ink-light shadow-sm focus:border-brand focus:ring-brand"
                        >
                    </div>
                    <button type="button" class="btn-secondary shrink-0">
                        <x-icon name="filter" size="16" />
                        Filter
                    </button>
                </div>
                <p class="mt-2 text-2xs text-ink-muted">Gunakan pencarian untuk menemukan dokumen secara cepat di seluruh Divisi Teknis.</p>
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
                                <th class="px-5 py-3 font-medium">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
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
                    <div class="flex flex-col items-center justify-center text-center gap-2 py-8">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-app border border-line text-ink-light">
                            <x-icon name="activity" size="20" />
                        </span>
                        <p class="text-sm font-medium text-ink">Belum ada aktivitas</p>
                        <p class="text-2xs text-ink-muted max-w-xs">Aktivitas pengguna akan tercatat otomatis di sini.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

</x-app-layout>
