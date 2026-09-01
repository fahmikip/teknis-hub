<x-app-layout :title="'Dokumen Terarsip'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Dokumen' => route('documents.index'), 'Terarsip' => null]" />

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-ink">Dokumen Terarsip</h1>
                <p class="mt-1 text-sm text-ink-muted">Dokumen yang diarsipkan (soft deleted) dapat dipulihkan</p>
            </div>
            <a href="{{ route('documents.index') }}" class="btn-secondary shrink-0">
                <x-icon name="file-text" size="16" />
                Kembali ke Semua Dokumen
            </a>
        </div>

        <form method="GET" action="{{ route('documents.archived') }}" class="card" aria-label="Cari dokumen terarsip">
            <div class="card-body">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                        <x-icon name="search" size="18" />
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari dokumen terarsip..."
                        class="w-full rounded-md border-line bg-white pl-11 pr-4 py-2.5 text-sm text-ink placeholder:text-ink-light shadow-sm focus:border-brand focus:ring-brand"
                    >
                </div>
            </div>
        </form>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-2xs uppercase tracking-wide text-ink-muted border-b border-line">
                            <th class="px-5 py-3 font-medium w-12">No</th>
                            <th class="px-5 py-3 font-medium">Dokumen</th>
                            <th class="px-5 py-3 font-medium">Nomor</th>
                            <th class="px-5 py-3 font-medium">Kategori</th>
                            <th class="px-5 py-3 font-medium">Tahun</th>
                            <th class="px-5 py-3 font-medium">Diarsipkan Pada</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($documents as $document)
                            <tr class="hover:bg-app/60 transition-colors">
                                <td class="px-5 py-3 text-ink-muted">{{ $documents->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3 font-medium text-ink">{{ $document->title }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $document->document_number ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $document->category?->name ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $document->year }}</td>
                                <td class="px-5 py-3 text-ink-muted whitespace-nowrap">
                                    {{ $document->deleted_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('restore', $document)
                                            <form method="POST" action="{{ route('documents.restore', $document) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn-action" onclick="return confirm('Pulihkan dokumen ini?')">
                                                    <x-icon name="refresh" size="14" />
                                                    Pulihkan
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16">
                                    <div class="flex flex-col items-center justify-center text-center gap-3">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-app border border-line text-ink-light">
                                            <x-icon name="archive" size="22" />
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-ink">Tidak ada dokumen terarsip</p>
                                            <p class="mt-1 text-2xs text-ink-muted max-w-xs">Tidak ada dokumen yang saat ini diarsipkan.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($documents->hasPages())
                <div class="px-5 py-4 border-t border-line">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
