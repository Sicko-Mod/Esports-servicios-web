<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CorreoInstitucional implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dominio = config('club.correo_institucional_dominio');
        $correo = strtolower((string) $value);

        if (! str_ends_with($correo, '@'.$dominio)) {
            $fail('El correo debe ser institucional (@'.$dominio.').');
        }
    }
}
