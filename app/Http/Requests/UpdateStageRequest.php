<?php

namespace App\Http\Requests;

use App\Enums\ElectionType;
use App\Models\Stage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('stage'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'election_type' => ['required', Rule::in(ElectionType::values())],
            'description' => ['nullable', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer', Rule::exists('stages', 'id')->whereNot('id', $this->route('stage')->id)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tahapan wajib diisi.',
            'election_type.required' => 'Jenis pemilihan wajib dipilih.',
            'election_type.in' => 'Jenis pemilihan yang dipilih tidak valid.',
            'parent_id.exists' => 'Tahapan induk yang dipilih tidak valid.',
        ];
    }
}