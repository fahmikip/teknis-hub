<x-app-layout :title="'Edit Kategori'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Kategori' => route('categories.index'), 'Edit' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Edit Kategori</h1>
            <p class="mt-1 text-sm text-ink-muted">Perbarui informasi kategori</p>
        </div>

        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('categories._form', ['category' => $category])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('categories.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
