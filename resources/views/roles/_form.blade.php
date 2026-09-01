<div class="space-y-6">
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Role</h3>
        </div>
        <div class="card-body grid grid-cols-1 gap-4">
            <div>
                <label for="name" class="label">Nama Role <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" maxlength="255" value="{{ old('name', $role?->name) }}"
                       class="input @error('name') border-danger @enderror">
                @error('name')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label for="description" class="label">Deskripsi</label>
                <textarea id="description" name="description" rows="3" maxlength="5000"
                          class="input @error('description') border-danger @enderror">{{ old('description', $role?->description) }}</textarea>
                @error('description')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Permissions</h3>
        </div>
        <div class="card-body space-y-6">
            @php $selectedPerms = old('permissions', $role?->permissions?->pluck('id')->all() ?? []); @endphp

            @forelse ($permissionGroups as $group => $groupPermissions)
                <div>
                    <p class="text-2xs font-semibold uppercase tracking-wide text-ink-muted mb-2">{{ $group ?: 'Lainnya' }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($groupPermissions as $permission)
                            <label class="inline-flex items-center gap-2 text-sm text-ink cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       @checked(in_array($permission->id, $selectedPerms))
                                       class="rounded border-line text-brand focus:ring-brand">
                                <span>{{ $permission->label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-muted">Tidak ada permission yang tersedia.</p>
            @endforelse
            @error('permissions')<x-input-error :messages="$message" class="mt-2" />@enderror
        </div>
    </section>
</div>