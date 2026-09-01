<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::allSettings();

        if (! isset($settings[$key])) {
            return $default;
        }

        $value = $settings[$key]->value;

        return match ($settings[$key]->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOL),
            'integer' => (int) $value,
            'json' => json_decode((string) $value, true),
            default => $value,
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): self
    {
        if (is_array($value) || is_object($value)) {
            $type = 'json';
            $value = json_encode($value);
        }

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                'type' => $type,
                'description' => $description,
            ]
        );

        static::flushCache();

        return $setting;
    }

    protected static function allSettings(): array
    {
        return Cache::remember('teknishub.settings', 86400, function () {
            return static::query()->pluck('value', 'key')->map(function ($value, $key) {
                $row = static::where('key', $key)->first();
                return (object) ['value' => $row->value, 'type' => $row->type];
            })->toArray();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('teknishub.settings');
    }
}