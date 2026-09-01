<x-app-layout :title="'Dokumen Favorit'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Favorit' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Dokumen Favorit</h1>
            <p class="mt-1 text-sm text-ink-muted">Dokumen yang Anda tandai untuk akses cepat</p>
        </div>

        @forelse ($favorites as $favorite)
            <div class="card">
                <div class="card-body flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-2xs font-medium uppercase tracking-wide text-ink-muted">Dokumen</p>
                        <a href="{{ route('documents.show', $favorite->document) }}" class="mt-1 text-sm font-semibold text-ink hover:text-brand hover:underline">
                            {{ $favorite->document->title }}
                        </a>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            @if ($favorite->document->category)
                                <x-badge color="neutral" :dot="false">{{ $favorite->document->category->name }}</x-badge>
                            @endif
                            @if ($favorite->document->documentType)
                                <x-badge color="info" :dot="false">{{ $favorite->document->documentType->name }}</x-badge>
                            @endif
                            <x-badge color="neutral" :dot="false">{{ $favorite->document->year }}</x-badge>
                        </div>
                        <p class="mt-2 text-2xs text-ink-muted">Ditandai {{ $favorite->created_at->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('favorites.destroy', $favorite) }}" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-action-danger" title="Hapus dari favorit">
                            <x-icon name="star" size="14" />
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body py-16 flex flex-col items-center justify-center text-center gap-4">
                    <span class="inline-flex items-center justify-center h-14 w-14 rounded-lg bg-app border border-line text-ink-light">
                        <x-icon name="star" size="26" />
                    </span>
                    <div>
                        <p class="text-lg font-semibold text-ink">Belum ada favorit</p>
                        <p class="mt-1 text-sm text-ink-muted max-w-md mx-auto">
                            Tandai dokumen penting menggunakan ikon bintang pada halaman daftar dokumen.
                        </p>
                    </div>
                    <a href="{{ route('documents.index') }}" class="btn-primary">
                        <x-icon name="files" size="16" />
                        Lihat Dokumen
                    </a>
                </div>
            </div>
        @endforelse

        @if ($favorites->hasPages())
            <div class="card">
                <div class="card-body">
                    {{ $favorites->links() }}
                </div>
            </div>
        @endif
    </div>

</x-app-layout>