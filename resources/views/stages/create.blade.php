<x-app-layout :title="'Tambah Tahapan'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Tahapan' => route('stages.index'), 'Tambah Tahapan' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Tambah Tahapan</h1>
            <p class="mt-1 text-sm text-ink-muted">Tambahkan tahapan baru ke TeknisHub</p>
        </div>

        <form method="POST" action="{{ route('stages.store') }}" class="space-y-6">
            @csrf
            @include('stages._form', ['stage' => null])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('stages.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Tahapan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>