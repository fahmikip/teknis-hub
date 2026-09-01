<?php

namespace App\Http\Requests;

use App\Enums\AccessLevel;
use App\Enums\DocumentStatus;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Document::class);
    }

    public function rules(): array
    {
        $year = date('Y');

        return [
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'document_type_id' => ['required', 'integer', Rule::exists('document_types', 'id')],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'stage_id' => ['nullable', 'integer', Rule::exists('stages', 'id')],
            'year' => ['required', 'integer', 'between:2000,' . ($year + 1)],
            'document_date' => ['nullable', 'date', 'before_or_equal:tomorrow'],
            'status' => ['required', Rule::in(DocumentStatus::values())],
            'access_level' => ['required', Rule::in(AccessLevel::values())],
            'description' => ['nullable', 'string', 'max:5000'],
            'keywords' => ['nullable', 'string', 'max:1000'],
            'file' => [
                'required',
                'file',
                'mimetypes:application/pdf',
                'max:' . config('documents.max_upload_size_kb'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File dokumen wajib diunggah.',
            'file.file' => 'File dokumen tidak valid.',
            'file.mimetypes' => 'File harus berupa PDF.',
            'file.max' => 'Ukuran file melebihi batas yang diperbolehkan.',
            'title.required' => 'Judul dokumen wajib diisi.',
            'document_type_id.required' => 'Jenis dokumen wajib dipilih.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'year.required' => 'Tahun wajib diisi.',
            'year.between' => 'Tahun harus antara 2000 dan '. (date('Y') + 1) .'.',
        ];
    }
}
