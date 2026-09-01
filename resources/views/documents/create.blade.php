<x-app-layout :title="'Tambah Dokumen'">

    <div class="space-y-6">
        <x-breadcrumb :crumbs="['Dashboard' => route('dashboard'), 'Dokumen' => route('documents.index'), 'Tambah Dokumen' => null]" />

        <div>
            <h1 class="text-xl font-semibold text-ink">Tambah Dokumen</h1>
            <p class="mt-1 text-sm text-ink-muted">Tambahkan dokumen ke arsip TeknisHub</p>
        </div>

        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf

            @include('documents._form', ['document' => null, 'filters' => $filters])

            {{-- File --}}
            <section class="card">
                <div class="card-header">
                    <h3 class="card-title">File Dokumen</h3>
                </div>
                <div class="card-body">
                    <div id="file-upload-wrap"
                         class="rounded-md border border-dashed border-line bg-app px-4 py-8 flex flex-col items-center justify-center text-center cursor-pointer hover:border-brand transition-colors">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-white border border-line text-ink-muted">
                            <x-icon name="upload" size="20" />
                        </span>
                        <p class="mt-3 text-sm font-medium text-ink">Upload PDF</p>
                        <p class="mt-1 text-2xs text-ink-muted">
                            Format PDF &middot; Maksimal {{ number_format(config('documents.max_upload_size_kb') / 1024, 0) }} MB
                        </p>
                        <input type="file" id="file" name="file" accept="application/pdf,.pdf"
                               class="sr-only" {{ $errors->has('file') ? '' : '' }}>
                    </div>

                    <div id="file-badge" class="mt-3 hidden">
                        <div class="flex items-center justify-between gap-3 rounded-md border border-line bg-white px-4 py-3">
                            <div class="min-w-0 flex items-center gap-3">
                                <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-red-50 text-brand shrink-0">
                                    <x-icon name="file-text" size="18" />
                                </span>
                                <div class="min-w-0">
                                    <p id="file-name" class="text-sm font-medium text-ink truncate"></p>
                                    <p id="file-size" class="text-2xs text-ink-muted"></p>
                                </div>
                            </div>
                            <button type="button" id="file-remove" class="btn-link shrink-0 text-danger hover:text-red-800">Remove</button>
                        </div>
                    </div>

                    @error('file')
                        <x-input-error :messages="$message" class="mt-2" />
                    @enderror
                </div>
            </section>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('documents.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" size="16" />
                    Simpan Dokumen
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const input = document.getElementById('file');
            const wrap = document.getElementById('file-upload-wrap');
            const badge = document.getElementById('file-badge');
            const nameEl = document.getElementById('file-name');
            const sizeEl = document.getElementById('file-size');
            const remove = document.getElementById('file-remove');

            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            function showFile() {
                if (input.files && input.files[0]) {
                    const f = input.files[0];
                    nameEl.textContent = f.name;
                    sizeEl.textContent = formatBytes(f.size);
                    badge.classList.remove('hidden');
                }
            }

            wrap.addEventListener('click', () => input.click());
            input.addEventListener('change', showFile);
            remove.addEventListener('click', (e) => {
                e.preventDefault();
                input.value = '';
                badge.classList.add('hidden');
            });
        </script>
    @endpush

</x-app-layout>
