<x-app-layout :title="'Tambah Kategori'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Kategori' => route('categories.index'), 'Tambah Kategori' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Tambah Kategori</h1>
            <p class="mt-1 text-sm text-ink-muted">Tambahkan kategori baru ke TeknisHub</p>
        </div>

        <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
            @csrf
            @include('categories._form', ['category' => null])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('categories.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
