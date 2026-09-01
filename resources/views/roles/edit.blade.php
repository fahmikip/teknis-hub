<x-app-layout :title="'Edit Role'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Role &amp; Permission' => route('roles.index'), 'Edit' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Edit Role</h1>
            <p class="mt-1 text-sm text-ink-muted">Perbarui role dan hak aksesnya</p>
        </div>

        <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('roles._form', ['role' => $role])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('roles.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>