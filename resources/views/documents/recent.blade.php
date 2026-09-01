<x-app-layout :title="'Dokumen Terbaru'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Dokumen' => route('documents.index'), 'Terbaru' => null]" />

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-ink">Dokumen Terbaru</h1>
                <p class="mt-1 text-sm text-ink-muted">10 dokumen terakhir yang diperbarui</p>
            </div>
            <a href="{{ route('documents.index') }}" class="btn-secondary shrink-0">
                <x-icon name="file-text" size="16" />
                Semua Dokumen
            </a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-2xs uppercase tracking-wide text-ink-muted border-b border-line">
                            <th class="px-5 py-3 font-medium w-12">No</th>
                            <th class="px-5 py-3 font-medium">Dokumen</th>
                            <th class="px-5 py-3 font-medium">Jenis</th>
                            <th class="px-5 py-3 font-medium">Kategori</th>
                            <th class="px-5 py-3 font-medium">Tahun</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Terakhir Diperbarui</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($documents as $index => $document)
                            <tr class="hover:bg-app/60 transition-colors">
                                <td class="px-5 py-3 text-ink-muted">{{ $index + 1 }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('documents.show', $document) }}" class="font-medium text-ink hover:text-brand">
                                        {{ $document->title }}
                                    </a>
                                </td>
                                <td class="px-5 py-3">{{ $document->documentType?->name ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $document->category?->name ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $document->year }}</td>
                                <td class="px-5 py-3">
                                    @if ($document->status instanceof \App\Enums\DocumentStatus)
                                        @php
                                            $map = [
                                                'draft' => 'neutral',
                                                'active' => 'success',
                                                'revised' => 'warning',
                                                'invalid' => 'danger',
                                                'archived' => 'gold',
                                            ];
                                        @endphp
                                        <x-badge :color="$map[$document->status->value] ?? 'neutral'">{{ $document->status->label() }}</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-ink-muted whitespace-nowrap">
                                    {{ $document->updated_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('documents.show', $document) }}" class="btn-action">
                                            <x-icon name="eye" size="14" />
                                            Lihat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16">
                                    <div class="flex flex-col items-center justify-center text-center gap-3">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-app border border-line text-ink-light">
                                            <x-icon name="clock" size="22" />
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-ink">Belum ada dokumen</p>
                                            <p class="mt-1 text-2xs text-ink-muted max-w-xs">Belum ada dokumen yang tersimpan di TeknisHub.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-app-layout>
