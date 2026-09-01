@php
    $statusOptions = \App\Enums\DocumentStatus::cases();
    $accessOptions = \App\Enums\AccessLevel::cases();
@endphp

<div class="space-y-6">
    {{-- Informasi Dokumen --}}
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Dokumen</h3>
        </div>
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="title" class="label">Judul Dokumen <span class="text-danger">*</span></label>
                <input type="text" id="title" name="title" maxlength="255"
                       value="{{ old('title', $document?->title) }}"
                       class="input @error('title') border-danger @enderror">
                @error('title')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="document_number" class="label">Nomor Dokumen</label>
                <input type="text" id="document_number" name="document_number" maxlength="255"
                       value="{{ old('document_number', $document?->document_number) }}"
                       class="input @error('document_number') border-danger @enderror">
                @error('document_number')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="year" class="label">Tahun <span class="text-danger">*</span></label>
                <input type="number" id="year" name="year" min="2000" max="{{ date('Y') + 1 }}"
                       value="{{ old('year', $document?->year ?? date('Y')) }}"
                       class="input @error('year') border-danger @enderror">
                @error('year')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="document_type_id" class="label">Jenis Dokumen <span class="text-danger">*</span></label>
                <select id="document_type_id" name="document_type_id"
                        class="select @error('document_type_id') border-danger @enderror">
                    <option value="">Pilih jenis</option>
                    @foreach ($filters['documentTypes'] as $type)
                        <option value="{{ $type->id }}" @selected(old('document_type_id', $document?->document_type_id) == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('document_type_id')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="category_id" class="label">Kategori <span class="text-danger">*</span></label>
                <select id="category_id" name="category_id"
                        class="select @error('category_id') border-danger @enderror">
                    <option value="">Pilih kategori</option>
                    @foreach ($filters['categories'] as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $document?->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="stage_id" class="label">Tahapan</label>
                <select id="stage_id" name="stage_id"
                        class="select @error('stage_id') border-danger @enderror">
                    <option value="">Pilih tahapan</option>
                    @foreach ($filters['stages']->groupBy(fn ($s) => $s->election_type?->value) as $electionValue => $groupedStages)
                        <optgroup label="{{ $electionValue ? \App\Enums\ElectionType::from($electionValue)->label() : 'Lainnya' }}">
                            @foreach ($groupedStages as $stage)
                                <option value="{{ $stage->id }}" @selected(old('stage_id', $document?->stage_id) == $stage->id)>{{ $stage->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('stage_id')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>

            <div>
                <label for="document_date" class="label">Tanggal Dokumen</label>
                <input type="date" id="document_date" name="document_date"
                       value="{{ old('document_date', $document?->document_date?->format('Y-m-d')) }}"
                       class="input @error('document_date') border-danger @enderror">
                @error('document_date')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
        </div>
    </section>

    {{-- Status --}}
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Status</h3>
        </div>
        <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="status" class="label">Status <span class="text-danger">*</span></label>
                <select id="status" name="status"
                        class="select @error('status') border-danger @enderror">
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $document?->status?->value ?? 'active') == $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                @error('status')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label for="access_level" class="label">Access Level <span class="text-danger">*</span></label>
                <select id="access_level" name="access_level"
                        class="select @error('access_level') border-danger @enderror">
                    @foreach ($accessOptions as $access)
                        <option value="{{ $access->value }}" @selected(old('access_level', $document?->access_level?->value ?? 'internal') == $access->value)>{{ $access->label() }}</option>
                    @endforeach
                </select>
                @error('access_level')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
        </div>
    </section>

    {{-- Deskripsi & Kata Kunci --}}
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Deskripsi</h3>
        </div>
        <div class="card-body grid grid-cols-1 gap-4">
            <div>
                <label for="description" class="label">Deskripsi</label>
                <textarea id="description" name="description" rows="4" maxlength="5000"
                          class="input @error('description') border-danger @enderror">{{ old('description', $document?->description) }}</textarea>
                @error('description')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
            <div>
                <label for="keywords" class="label">Kata Kunci</label>
                <input type="text" id="keywords" name="keywords" maxlength="1000"
                       value="{{ old('keywords', $document?->keywords) }}"
                       class="input @error('keywords') border-danger @enderror"
                       placeholder="dipisahkan dengan koma">
                @error('keywords')<x-input-error :messages="$message" class="mt-1" />@enderror
            </div>
        </div>
    </section>
</div>
