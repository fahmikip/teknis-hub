<x-app-layout :title="$document->title">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Dokumen' => route('documents.index'), $document->title => null]" />

        {{-- Header --}}
        <div class="card">
            <div class="card-body">
                <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Dokumen</p>
                <div class="mt-2 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold text-ink leading-snug">{{ $document->title }}</h1>
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
                            <div class="mt-2">
                                <x-badge :color="$map[$document->status->value] ?? 'neutral'">{{ $document->status->label() }}</x-badge>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('favorites.toggle', $document) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-secondary" title="Tandai / hapus dari favorit">
                                <x-icon name="star" size="16" class="text-gold" />
                            </button>
                        </form>
                        @can('preview', $document)
                            <a href="{{ route('documents.preview', $document) }}" target="_blank" class="btn-secondary" title="Pratinjau PDF">
                                <x-icon name="external" size="16" />
                                Preview
                            </a>
                        @endcan
                        @can('download', $document)
                            <a href="{{ route('documents.download', $document) }}" class="btn-secondary">
                                <x-icon name="download" size="16" />
                                Download
                            </a>
                        @endcan
                        @can('update', $document)
                            <a href="{{ route('documents.edit', $document) }}" class="btn-primary">
                                <x-icon name="edit" size="16" />
                                Edit
                            </a>
                        @endcan
                        @can('delete', $document)
                            <form method="POST" action="{{ route('documents.destroy', $document) }}"
                                  onsubmit="return confirm('Arsipkan dokumen ini? Dokumen dapat dipulihkan kembali.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">
                                    <x-icon name="archive" size="16" />
                                    Arsipkan
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Metadata --}}
            <div class="lg:col-span-2 space-y-6">
                <section class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Dokumen</h3>
                    </div>
                    <div class="card-body">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Nomor Dokumen</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $document->document_number ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Jenis Dokumen</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $document->documentType?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Kategori</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $document->category?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Tahapan</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $document->stage?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Tahun</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $document->year }}</dd>
                            </div>
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Tanggal Dokumen</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $document->document_date ? $document->document_date->format('d F Y') : '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Access Level</dt>
                                <dd class="mt-1">
                                    @if ($document->access_level instanceof \App\Enums\AccessLevel)
                                        @php
                                            $levelMap = ['internal' => 'info', 'restricted' => 'warning', 'public' => 'success'];
                                        @endphp
                                        <x-badge :color="$levelMap[$document->access_level->value] ?? 'neutral'">{{ $document->access_level->label() }}</x-badge>
                                    @else
                                        <span class="text-sm text-ink">{{ $document->access_level }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Diunggah oleh</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $document->creator?->name ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h3 class="card-title">Deskripsi</h3>
                    </div>
                    <div class="card-body">
                        @if ($document->description)
                            <p class="text-sm text-ink leading-relaxed whitespace-pre-line">{{ $document->description }}</p>
                        @else
                            <p class="text-sm text-ink-muted">Tidak ada deskripsi.</p>
                        @endif

                        @if ($document->keywords)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach (explode(',', $document->keywords) as $keyword)
                                    @if (trim($keyword))
                                        <x-badge color="neutral" :dot="false">{{ trim($keyword) }}</x-badge>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            {{-- Riwayat Versi --}}
            <section class="card">
                <div class="card-header flex items-center justify-between gap-3">
                    <h3 class="card-title">Riwayat Versi</h3>
                    @if ($document->versions->count())
                        <x-badge color="brand">{{ $document->versions->count() }} versi</x-badge>
                    @endif
                </div>
                <div class="card-body">
                    @can('manageVersions', $document)
                        <form method="POST" action="{{ route('documents.versions.store', $document) }}"
                              enctype="multipart/form-data" class="mb-5 p-4 rounded-md bg-app border border-line space-y-3">
                            @csrf
                            <p class="text-sm font-medium text-ink">Unggah Versi Baru</p>
                            <div>
                                <input type="file" name="file" accept="application/pdf"
                                       class="input @error('file') border-danger @enderror">
                                @error('file')<x-input-error :messages="$message" class="mt-1" />@enderror
                            </div>
                            <div>
                                <input type="text" name="notes" maxlength="2000" placeholder="Catatan versi (opsional)"
                                       class="input @error('notes') border-danger @enderror">
                                @error('notes')<x-input-error :messages="$message" class="mt-1" />@enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary">
                                    <x-icon name="upload" size="16" />
                                    Unggah
                                </button>
                            </div>
                        </form>
                    @endcan

                    @forelse ($document->versions as $version)
                        <div class="flex items-start justify-between gap-3 py-3 {{ ! $loop->first ? 'border-t border-line' : '' }}">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-ink">v{{ $version->version_number }}</span>
                                    @if ($version->id === $document->latestVersion?->id)
                                        <x-badge color="gold" :dot="false">Terbaru</x-badge>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-ink truncate">{{ $version->original_filename ?? '—' }}</p>
                                <p class="mt-0.5 text-2xs text-ink-muted">
                                    {{ $version->created_at->format('d F Y H:i') }} &middot;
                                    {{ $version->uploader?->name ?? 'Sistem' }}
                                    @if ($version->notes)
                                        &middot; {{ $version->notes }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @can('download', $document)
                                    <a href="{{ route('documents.versions.download', [$document, $version]) }}" class="btn-action" title="Unduh versi ini">
                                        <x-icon name="download" size="14" />
                                    </a>
                                @endcan
                                @can('manageVersions', $document)
                                    @if ($version->version_number > 1)
                                        <form method="POST" action="{{ route('documents.versions.destroy', [$document, $version]) }}"
                                              onsubmit="return confirm('Hapus versi ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-danger" title="Hapus versi">
                                                <x-icon name="trash" size="14" />
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-2xs text-ink-light" title="Versi awal tidak dapat dihapus">dilindungi</span>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-ink-muted">Belum ada file terlampir.</p>
                    @endforelse
                </div>
            </section>

            {{-- Informasi File --}}
            <aside class="space-y-6">
                <section class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi File</h3>
                    </div>
                    <div class="card-body">
                        @if ($document->latestVersion)
                            <dl class="space-y-3">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-red-50 text-brand shrink-0">
                                        <x-icon name="file-text" size="18" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-ink truncate">{{ $document->latestVersion->original_filename ?? '—' }}</p>
                                    </div>
                                </div>
                                <div class="border-t border-line pt-3 space-y-3">
                                    <div>
                                        <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Ukuran</dt>
                                        <dd class="mt-1 text-sm text-ink">
                                            @if ($document->latestVersion->file_size)
                                                {{ number_format($document->latestVersion->file_size / 1048576, 2) }} MB
                                            @else
                                                —
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Format</dt>
                                        <dd class="mt-1 text-sm text-ink uppercase">{{ $document->latestVersion->mime_type ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Tanggal Upload</dt>
                                        <dd class="mt-1 text-sm text-ink">{{ $document->latestVersion->created_at->format('d F Y H:i') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Versi Saat Ini</dt>
                                        <dd class="mt-1">
                                            <x-badge color="brand">v{{ $document->latestVersion->version_number }}</x-badge>
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        @else
                            <p class="text-sm text-ink-muted">Tidak ada file terlampir.</p>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </div>

</x-app-layout>
