<x-app-layout :title="'Edit Pengguna'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Pengguna' => route('users.index'), 'Edit' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Edit Pengguna</h1>
            <p class="mt-1 text-sm text-ink-muted">Perbarui informasi akun pengguna</p>
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('users._form', ['user' => $user])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>