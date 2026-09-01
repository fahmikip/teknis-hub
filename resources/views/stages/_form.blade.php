<div class="space-y-6">
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Tahapan</h3>
        </div>
        <div class="card-body grid grid-cols-1 gap-4">
            <div>
                <label for="name" class="label">Nama Tahapan <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" maxlength="255"
                       value="{{ old('name', $stage?->name) }}"
                       class="input @error('name') border-danger @enderror">
                @error('name')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="election_type" class="label">Jenis Pemilihan <span class="text-danger">*</span></label>
                <select id="election_type" name="election_type" class="select @error('election_type') border-danger @enderror">
                    @foreach ($electionTypes as $type)
                        <option value="{{ $type->value }}"
                                @selected(old('election_type', $stage?->election_type?->value) === $type->value)>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                @error('election_type')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="parent_id" class="label">Tahapan Induk</label>
                <select id="parent_id" name="parent_id" class="select @error('parent_id') border-danger @enderror">
                    <option value="">Tidak ada (tahapan utama)</option>
                    @foreach ($stages as $candidate)
                        <option value="{{ $candidate->id }}"
                                @selected(old('parent_id', $stage?->parent_id) == $candidate->id)>
                            {{ $candidate->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="sort_order" class="label">Urutan</label>
                <input type="number" id="sort_order" name="sort_order" min="0" max="9999"
                       value="{{ old('sort_order', $stage?->sort_order ?? 0) }}"
                       class="input @error('sort_order') border-danger @enderror">
                @error('sort_order')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="description" class="label">Deskripsi</label>
                <textarea id="description" name="description" rows="4" maxlength="5000"
                          class="input @error('description') border-danger @enderror">{{ old('description', $stage?->description) }}</textarea>
                @error('description')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $stage?->is_active ?? true))
                           class="rounded border-line text-brand focus:ring-brand">
                    <span class="text-sm text-ink">Aktif</span>
                </label>
            </div>
        </div>
    </section>
</div>