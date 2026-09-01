<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', \App\Models\Setting::class);
    }

    public function rules(): array
    {
        return [
            'app_name' => ['nullable', 'string', 'max:255'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'institution_region' => ['nullable', 'string', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:2000'],
            'max_upload_size' => ['nullable', 'integer', 'min:1024', 'max:102400'],
        ];
    }

    public function messages(): array
    {
        return [
            'max_upload_size.integer' => 'Ukuran upload maksimal harus berupa angka.',
            'max_upload_size.min' => 'Ukuran upload maksimal minimal 1 MB (1024 KB).',
        ];
    }
}