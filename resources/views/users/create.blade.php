<x-app-layout :title="'Tambah Pengguna'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Pengguna' => route('users.index'), 'Tambah Pengguna' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Tambah Pengguna</h1>
            <p class="mt-1 text-sm text-ink-muted">Tambahkan akun pengguna baru</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf
            @include('users._form', ['user' => null])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>

</x-app-layout>