<div class="space-y-6">
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Akun</h3>
        </div>
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" maxlength="255" value="{{ old('name', $user?->name) }}"
                       class="input @error('name') border-danger @enderror">
                @error('name')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label for="username" class="label">Username <span class="text-danger">*</span></label>
                <input type="text" id="username" name="username" maxlength="255" value="{{ old('username', $user?->username) }}"
                       class="input @error('username') border-danger @enderror">
                @error('username')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label for="email" class="label">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" maxlength="255" value="{{ old('email', $user?->email) }}"
                       class="input @error('email') border-danger @enderror">
                @error('email')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label for="password" class="label">Password {{ $user ? '(kosongkan bila tidak diubah)' : '*' }}</label>
                <input type="password" id="password" name="password" autocomplete="new-password"
                       class="input @error('password') border-danger @enderror">
                @error('password')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label for="password_confirmation" class="label">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                       class="input @error('password_confirmation') border-danger @enderror">
                @error('password_confirmation')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer pt-6">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $user?->is_active ?? true))
                           class="rounded border-line text-brand focus:ring-brand">
                    <span class="text-sm text-ink">Aktif</span>
                </label>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Role</h3>
        </div>
        <div class="card-body">
            <div class="flex flex-wrap gap-2">
                @php $selectedRoles = old('roles', $user?->roles?->pluck('id')->all() ?? []); @endphp
                @foreach ($roles as $role)
                    <label class="inline-flex items-center gap-2 rounded-md bg-app border border-line px-3 py-2 cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                               @checked(in_array($role->id, $selectedRoles))
                               class="rounded border-line text-brand focus:ring-brand">
                        <span class="text-sm text-ink">{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('roles')<x-input-error :messages="$message" class="mt-2" />@enderror
        </div>
    </section>
</div>