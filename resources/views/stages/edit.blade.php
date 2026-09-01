<x-app-layout :title="'Edit Tahapan'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Tahapan' => route('stages.index'), 'Edit' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Edit Tahapan</h1>
            <p class="mt-1 text-sm text-ink-muted">Perbarui informasi tahapan</p>
        </div>

        <form method="POST" action="{{ route('stages.update', $stage) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('stages._form', ['stage' => $stage])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('stages.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>