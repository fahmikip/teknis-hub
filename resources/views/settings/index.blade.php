<x-app-layout :title="'Pengaturan'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Pengaturan' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Pengaturan</h1>
            <p class="mt-1 text-sm text-ink-muted">Kelola pengaturan umum aplikasi</p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="card">
                <div class="card-header">
                    <h3 class="card-title">Identitas</h3>
                </div>
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="app_name" class="label">Nama Aplikasi</label>
                        <input type="text" id="app_name" name="app_name" maxlength="255"
                               value="{{ old('app_name', $settings['app_name']->value) }}"
                               class="input @error('app_name') border-danger @enderror">
                        @error('app_name')<x-input-error :messages="$message" class="mt-1" />@enderror
                    </div>
                    <div>
                        <label for="institution_name" class="label">Nama Instansi</label>
                        <input type="text" id="institution_name" name="institution_name" maxlength="255"
                               value="{{ old('institution_name', $settings['institution_name']->value) }}"
                               class="input @error('institution_name') border-danger @enderror">
                        @error('institution_name')<x-input-error :messages="$message" class="mt-1" />@enderror
                    </div>
                    <div>
                        <label for="institution_region" class="label">Kabupaten/Kota</label>
                        <input type="text" id="institution_region" name="institution_region" maxlength="255"
                               value="{{ old('institution_region', $settings['institution_region']->value) }}"
                               class="input @error('institution_region') border-danger @enderror">
                        @error('institution_region')<x-input-error :messages="$message" class="mt-1" />@enderror
                    </div>
                    <div>
                        <label for="footer_text" class="label">Teks Footer</label>
                        <input type="text" id="footer_text" name="footer_text" maxlength="2000"
                               value="{{ old('footer_text', $settings['footer_text']->value) }}"
                               class="input @error('footer_text') border-danger @enderror">
                        @error('footer_text')<x-input-error :messages="$message" class="mt-1" />@enderror
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload</h3>
                </div>
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="max_upload_size" class="label">Ukuran Upload Maksimal (KB)</label>
                        <input type="number" id="max_upload_size" name="max_upload_size" min="1024" max="102400"
                               value="{{ old('max_upload_size', $settings['max_upload_size']->value) }}"
                               class="input @error('max_upload_size') border-danger @enderror">
                        @error('max_upload_size')<x-input-error :messages="$message" class="mt-1" />@enderror
                        <p class="mt-1 text-2xs text-ink-muted">Minimum 1024 KB (1 MB), maksimum 102400 KB (100 MB).</p>
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

</x-app-layout>