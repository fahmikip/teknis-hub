<x-app-layout :title="'Tambah Jenis Dokumen'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Jenis Dokumen' => route('document-types.index'), 'Tambah Jenis Dokumen' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Tambah Jenis Dokumen</h1>
            <p class="mt-1 text-sm text-ink-muted">Tambahkan jenis dokumen baru ke TeknisHub</p>
        </div>

        <form method="POST" action="{{ route('document-types.store') }}" class="space-y-6">
            @csrf
            @include('document-types._form', ['documentType' => null])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('document-types.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Jenis Dokumen
                </button>
            </div>
        </form>
    </div>

</x-app-layout>