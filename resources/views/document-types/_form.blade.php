<div class="space-y-6">
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Jenis Dokumen</h3>
        </div>
        <div class="card-body grid grid-cols-1 gap-4">
            <div>
                <label for="name" class="label">Nama Jenis Dokumen <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" maxlength="255"
                       value="{{ old('name', $documentType?->name) }}"
                       class="input @error('name') border-danger @enderror">
                @error('name')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="description" class="label">Deskripsi</label>
                <textarea id="description" name="description" rows="4" maxlength="5000"
                          class="input @error('description') border-danger @enderror">{{ old('description', $documentType?->description) }}</textarea>
                @error('description')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $documentType?->is_active ?? true))
                           class="rounded border-line text-brand focus:ring-brand">
                    <span class="text-sm text-ink">Aktif</span>
                </label>
            </div>
        </div>
    </section>
</div>