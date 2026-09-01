<x-app-layout :title="'Tambah Role'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Role &amp; Permission' => route('roles.index'), 'Tambah Role' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Tambah Role</h1>
            <p class="mt-1 text-sm text-ink-muted">Tambahkan role baru beserta hak aksesnya</p>
        </div>

        <form method="POST" action="{{ route('roles.store') }}" class="space-y-6">
            @csrf
            @include('roles._form', ['role' => null])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('roles.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Role
                </button>
            </div>
        </form>
    </div>

</x-app-layout>