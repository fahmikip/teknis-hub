<x-app-layout>
    <div class="space-y-6">
        <div>
            <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), $title => null]" />
        </div>

        <div class="card">
            <div class="card-body py-16 flex flex-col items-center justify-center text-center gap-4">
                <span class="inline-flex items-center justify-center h-14 w-14 rounded-lg bg-app border border-line text-ink-muted">
                    <x-icon name="inbox" size="26" />
                </span>
                <div>
                    <h2 class="text-lg font-semibold text-ink">{{ $title }}</h2>
                    <p class="mt-1 text-sm text-ink-muted max-w-md mx-auto">
                        Modul ini akan tersedia pada tahap pengembangan berikutnya.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    <x-icon name="dashboard" size="16" />
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
