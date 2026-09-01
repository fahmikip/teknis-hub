<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Setting::class);

        $editableKeys = [
            'app_name' => 'Nama Aplikasi',
            'institution_name' => 'Nama Instansi',
            'institution_region' => 'Kabupaten/Kota',
            'footer_text' => 'Teks Footer',
            'max_upload_size' => 'Ukuran Upload Maksimal (KB)',
        ];

        $settings = collect($editableKeys)->mapWithKeys(fn ($label, $key) => [
            $key => (object) [
                'key' => $key,
                'label' => $label,
                'value' => Setting::get($key, ''),
                'type' => Setting::query()->where('key', $key)->value('type') ?? 'string',
                'description' => Setting::query()->where('key', $key)->value('description'),
            ],
        ]);

        return view('settings.index', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->authorize('update', Setting::class);

        $fields = [
            'app_name', 'institution_name', 'institution_region', 'footer_text', 'max_upload_size',
        ];

        foreach ($fields as $field) {
            $value = $request->input($field);
            $type = Setting::query()->where('key', $field)->value('type') ?? 'string';

            if ($type === 'integer') {
                $value = $value === null || $value === '' ? null : (int) $value;
            }

            Setting::set($field, $value, $type);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}