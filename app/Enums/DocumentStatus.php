<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Revised = 'revised';
    case Invalid = 'invalid';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Aktif',
            self::Revised => 'Diubah',
            self::Invalid => 'Tidak Berlaku',
            self::Archived => 'Diarsipkan',
        };
    }

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}