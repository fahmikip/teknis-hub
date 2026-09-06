<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Kata sandi tidak valid.');

            return;
        }

        if ($this->isSequential($value)) {
            $fail('Kata sandi terlalu sederhana atau berurutan.');

            return;
        }

        if (preg_match('/(.)\1{3,}/', $value)) {
            $fail('Kata sandi tidak boleh memiliki 4 karakter berulang berturut-turut.');
        }
    }

    protected function isSequential(string $value): bool
    {
        $lower = strtolower($value);

        $sequences = [
            'abcdefghijklmnopqrstuvwxyz',
            'zyxwvutsrqponmlkjihgfedcba',
            '0123456789',
            '9876543210',
            'qwertyuiop',
            'poiuytrewq',
            'asdfghjkl',
            'lkjhgfdsa',
            'zxcvbnm',
            'mnbvcxz',
        ];

        foreach ($sequences as $seq) {
            if (str_contains($seq, $lower)) {
                return true;
            }
        }

        $patterns = ['password', 'admin', 'letmein', 'qwerty', '123456', 'teknishub'];
        foreach ($patterns as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
