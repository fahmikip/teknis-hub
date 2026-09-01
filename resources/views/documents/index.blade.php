<x-app-layout :title="'Dokumen'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Dokumen' => null]" />

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-ink">Dokumen</h1>
                <p class="mt-1 text-sm text-ink-muted">Kelola seluruh dokumen Divisi Teknis</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('documents.export', request()->query()) }}" class="btn-secondary shrink-0">
                    <x-icon name="download" size="16" />
                    Export CSV
                </a>
                @can('create', App\Models\Document::class)
                    <a href="{{ route('documents.create') }}" class="btn-primary shrink-0">
                        <x-icon name="plus" size="16" />
                        Tambah Dokumen
                    </a>
                @endcan
            </div>
        </div>

        {{-- Filter & pencarian --}}
        <form
            method="GET"
            action="{{ route('documents.index') }}"
            class="card"
            aria-label="Filter dokumen"
            x-data="{ q: '{{ request('q') }}' }"
        >
            <div class="card-body space-y-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-light">
                        <x-icon name="search" size="18" />
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari judul, nomor, deskripsi, kata kunci, kategori, jenis, tahapan..."
                        class="w-full rounded-md border-line bg-white pl-11 pr-4 py-2.5 text-sm text-ink placeholder:text-ink-light shadow-sm focus:border-brand focus:ring-brand"
                        x-model="q"
                        x-on:input.debounce.400ms="if (q.trim() !== '' || '{{ request('q') }}' !== '') { $el.form.submit(); }"
                    >
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
                    <div>
                        <label for="year" class="label">Tahun</label>
                        <select name="year" id="year" class="select">
                            <option value="">Semua Tahun</option>
                            @foreach ($filters['years'] as $year)
                                <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="category_id" class="label">Kategori</label>
                        <select name="category_id" id="category_id" class="select">
                            <option value="">Semua Kategori</option>
                            @foreach ($filters['categories'] as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="document_type_id" class="label">Jenis</label>
                        <select name="document_type_id" id="document_type_id" class="select">
                            <option value="">Semua Jenis</option>
                            @foreach ($filters['documentTypes'] as $type)
                                <option value="{{ $type->id }}" @selected(request('document_type_id') == $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="stage_id" class="label">Tahapan</label>
                        <select name="stage_id" id="stage_id" class="select">
                            <option value="">Semua Tahapan</option>
                            @foreach ($filters['stages'] as $stage)
                                <option value="{{ $stage->id }}" @selected(request('stage_id') == $stage->id)>{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="label">Status</label>
                        <select name="status" id="status" class="select">
                            <option value="">Semua Status</option>
                            @foreach (\App\Enums\DocumentStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected(request('status') == $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="access_level" class="label">Akses</label>
                        <select name="access_level" id="access_level" class="select">
                            <option value="">Semua Akses</option>
                            @foreach ($filters['accessLevels'] as $level)
                                <option value="{{ $level->value }}" @selected(request('access_level') == $level->value)>{{ $level->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <span class="label">&nbsp;</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" max="{{ request('date_to', now()->format('Y-m-d')) }}" class="select" aria-label="Tanggal dari">
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
                    <div class="col-span-2 md:col-span-3 lg:col-span-6">
                        <span class="label">Sampai tanggal</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" min="{{ request('date_from') }}" class="select" aria-label="Tanggal sampai">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn-secondary">
                        <x-icon name="filter" size="16" />
                        Terapkan
                    </button>
                    @if ($activeFilterCount > 0)
                        <a href="{{ route('documents.index') }}" class="btn-link">Reset ({{ $activeFilterCount }})</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Active filter chips --}}
        @if ($activeFilterCount > 0)
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-2xs uppercase tracking-wide text-ink-muted">Filter aktif:</span>
                @if (request('q'))
                    <a href="{{ route('documents.index', request()->except(['q', 'page'])) }}" class="chip">Cari: "{{ request('q') }}" <x-icon name="x" size="12" /></a>
                @endif
                @if (request('year'))
                    <a href="{{ route('documents.index', request()->except(['year', 'page'])) }}" class="chip">Tahun: {{ request('year') }} <x-icon name="x" size="12" /></a>
                @endif
                @if (request('category_id'))
                    <a href="{{ route('documents.index', request()->except(['category_id', 'page'])) }}" class="chip">Kategori: {{ optional($filters['categories']->firstWhere('id', request('category_id')))->name }} <x-icon name="x" size="12" /></a>
                @endif
                @if (request('document_type_id'))
                    <a href="{{ route('documents.index', request()->except(['document_type_id', 'page'])) }}" class="chip">Jenis: {{ optional($filters['documentTypes']->firstWhere('id', request('document_type_id')))->name }} <x-icon name="x" size="12" /></a>
                @endif
                @if (request('stage_id'))
                    <a href="{{ route('documents.index', request()->except(['stage_id', 'page'])) }}" class="chip">Tahapan: {{ optional($filters['stages']->firstWhere('id', request('stage_id')))->name }} <x-icon name="x" size="12" /></a>
                @endif
                @if (request('status'))
                    <a href="{{ route('documents.index', request()->except(['status', 'page'])) }}" class="chip">Status: {{ \App\Enums\DocumentStatus::tryFrom(request('status'))?->label() ?? request('status') }} <x-icon name="x" size="12" /></a>
                @endif
                @if (request('access_level'))
                    <a href="{{ route('documents.index', request()->except(['access_level', 'page'])) }}" class="chip">Akses: {{ \App\Enums\AccessLevel::tryFrom(request('access_level'))?->label() }} <x-icon name="x" size="12" /></a>
                @endif
                @if (request('date_from') || request('date_to'))
                    <a href="{{ route('documents.index', request()->except(['date_from', 'date_to', 'page'])) }}" class="chip">Tanggal: {{ request('date_from') }} → {{ request('date_to') }} <x-icon name="x" size="12" /></a>
                @endif
            </div>
        @endif

        {{-- Sort & per page --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm">
            <div class="flex items-center text-ink-muted">
                <span>{{ $documents->total() }} dokumen</span>
            </div>
            <div class="flex items-center gap-3">
                <label for="per_page" class="text-ink-muted">Per halaman:</label>
                <form method="GET" action="{{ route('documents.index') }}" class="inline" x-data>
                    @foreach (request()->except(['per_page', 'page']) as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="per_page" class="select w-auto" x-on:change="this.form.submit()">
                        @foreach ([15, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected((int) request('per_page', 15) === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="flex items-center">
                    <label for="sort" class="text-ink-muted mr-2">Urutkan:</label>
                    <form method="GET" action="{{ route('documents.index') }}" class="inline">
                        @foreach (request()->except(['sort', 'direction', 'page', 'per_page']) as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                        <select name="sort" class="select w-auto" onchange="this.form.submit()">
                            <option value="created_at" @selected(request('sort', 'created_at') == 'created_at')>Terbaru</option>
                            <option value="title" @selected(request('sort') == 'title')>Judul</option>
                            <option value="year" @selected(request('sort') == 'year')>Tahun</option>
                            <option value="document_date" @selected(request('sort') == 'document_date')>Tanggal Dokumen</option>
                        </select>
                    </form>
                    <a
                        href="{{ route('documents.index', array_merge(request()->except(['direction', 'page']), ['direction' => request('direction', 'desc') === 'desc' ? 'asc' : 'desc'])) }}"
                        class="ml-2 p-1.5 rounded-md hover:bg-app text-ink-muted hover:text-ink transition-colors"
                        title="Ganti arah urut"
                    >
                        <x-icon name="{{ request('direction', 'desc') === 'desc' ? 'chevron-down' : 'chevron-up' }}" size="16" />
                    </a>
                </div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-2xs uppercase tracking-wide text-ink-muted border-b border-line">
                            <th class="px-5 py-3 font-medium w-12">No</th>
                            <th class="px-5 py-3 font-medium">Dokumen</th>
                            <th class="px-5 py-3 font-medium">Nomor</th>
                            <th class="px-5 py-3 font-medium">Jenis</th>
                            <th class="px-5 py-3 font-medium">Kategori</th>
                            <th class="px-5 py-3 font-medium">Tahun</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Akses</th>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($documents as $document)
                            <tr class="hover:bg-app/60 transition-colors">
                                <td class="px-5 py-3 text-ink-muted">{{ $documents->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('documents.show', $document) }}" class="font-medium text-ink hover:text-brand">
                                        {{ $document->title }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-ink-muted">{{ $document->document_number ?? '—' }}</td>
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
                                    @else
                                        <x-badge color="neutral">{{ $document->status }}</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($document->access_level instanceof \App\Enums\AccessLevel)
                                        @php
                                            $accessMap = [
                                                'internal' => 'brand',
                                                'restricted' => 'warning',
                                                'public' => 'info',
                                            ];
                                        @endphp
                                        <x-badge :color="$accessMap[$document->access_level->value] ?? 'neutral'">{{ $document->access_level->label() }}</x-badge>
                                    @else
                                        <x-badge color="neutral">{{ $document->access_level }}</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-ink-muted whitespace-nowrap">
                                    {{ $document->document_date ? $document->document_date->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('documents.show', $document) }}" class="btn-action">
                                            <x-icon name="eye" size="14" />
                                            Lihat
                                        </a>
                                        @can('update', $document)
                                            <a href="{{ route('documents.edit', $document) }}" class="btn-action">
                                                <x-icon name="edit" size="14" />
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-16">
                                    <div class="flex flex-col items-center justify-center text-center gap-3">
                                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-app border border-line text-ink-light">
                                            <x-icon name="inbox" size="22" />
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-ink">{{ request('q') || $activeFilterCount > 0 ? 'Tidak ada hasil yang cocok' : 'Belum ada dokumen' }}</p>
                                            <p class="mt-1 text-2xs text-ink-muted max-w-xs">
                                                {{ request('q') || $activeFilterCount > 0 ? 'Tidak ada dokumen yang cocok dengan pencarian atau filter saat ini. Coba ubah kata kunci atau reset filter.' : 'Belum ada dokumen yang tersimpan di TeknisHub.' }}
                                            </p>
                                        </div>
                                        @can('create', App\Models\Document::class)
                                            <a href="{{ route('documents.create') }}" class="btn-primary">Tambah Dokumen</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($documents->hasPages())
                <div class="px-5 py-4 border-t border-line flex items-center justify-between gap-4 flex-col sm:flex-row">
                    <span class="text-2xs text-ink-muted">
                        Menampilkan {{ $documents->firstItem() ?? 0 }}–{{ $documents->lastItem() ?? 0 }} dari {{ $documents->total() }} dokumen
                    </span>
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .chip {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                padding: 0.25rem 0.625rem;
                font-size: 0.75rem;
                color: var(--color-ink);
                background: var(--color-app);
                border: 1px solid var(--color-line);
                border-radius: 9999px;
                transition: all .15s;
            }
            .chip:hover {
                color: var(--color-brand);
                border-color: var(--color-brand);
            }
        </style>
    @endpush

</x-app-layout>
