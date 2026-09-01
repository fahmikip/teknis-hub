<?php

namespace App\Enums;

enum AccessLevel: string
{
    case Internal = 'internal';
    case Restricted = 'restricted';
    case Public = 'public';

    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Internal',
            self::Restricted => 'Terbatas',
            self::Public => 'Publik',
        };
    }

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}