<x-app-layout :title="'Edit Dokumen'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Dokumen' => route('documents.index'), $document->title => route('documents.show', $document), 'Edit' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Edit Dokumen</h1>
            <p class="mt-1 text-sm text-ink-muted">Perbarui metadata dokumen</p>
        </div>

        <form method="POST" action="{{ route('documents.update', $document) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('documents._form', ['document' => $document, 'filters' => $filters])

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('documents.show', $document) }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
