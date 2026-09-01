<x-app-layout :title="'Edit Jenis Dokumen'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Jenis Dokumen' => route('document-types.index'), 'Edit' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Edit Jenis Dokumen</h1>
            <p class="mt-1 text-sm text-ink-muted">Perbarui informasi jenis dokumen</p>
        </div>

        <form method="POST" action="{{ route('document-types.update', $documentType) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('document-types._form', ['documentType' => $documentType])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('document-types.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>