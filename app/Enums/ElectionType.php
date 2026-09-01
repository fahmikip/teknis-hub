<?php

namespace App\Enums;

enum ElectionType: string
{
    case General = 'general';
    case Pemilu = 'pemilu';
    case Pilkada = 'pilkada';

    public function label(): string
    {
        return match ($this) {
            self::General => 'Umum',
            self::Pemilu => 'Pemilu',
            self::Pilkada => 'Pilkada',
        };
    }

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}