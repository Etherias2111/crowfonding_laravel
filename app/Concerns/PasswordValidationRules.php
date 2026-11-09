<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, Password|ValidationRule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed', function ($attribute, $value, $fail) {
            if (! preg_match('/[^A-Za-z0-9]/', $value)) $fail('Incluye al menos un carácter especial.');
            if (preg_match('/([0-9])\1\1/', $value)) $fail('No repitas el mismo dígito tres o más veces seguidas.');
            if (preg_match_all('/(?=([0-9]{3}))/', $value, $matches)) {
                foreach ($matches[1] as $digits) {
                    $a = (int) $digits[0]; $b = (int) $digits[1]; $c = (int) $digits[2];
                    if (($b === $a + 1 && $c === $b + 1) || ($b === $a - 1 && $c === $b - 1)) {
                        $fail('No uses secuencias numéricas de tres cifras, como 123 o 321.'); break;
                    }
                }
            }
            if (preg_match('/([0-9])\1([0-9])\2/', $value, $pairs) && abs((int) $pairs[1] - (int) $pairs[2]) === 1) {
                $fail('No uses pares numéricos consecutivos como 2211 o 1122.');
            }
        }];
    }

    /**
     * Get the validation rules used to validate the current password.
     *
     * @return array<int, Password|ValidationRule|array<mixed>|string>
     */
    protected function currentPasswordRules(): array
    {
        return ['required', 'string', 'current_password'];
    }
}
