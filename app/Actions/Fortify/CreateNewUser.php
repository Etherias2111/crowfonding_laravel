<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a natural person or legal entity account.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        if (isset($input['email']) && is_string($input['email'])) {
            $input['email'] = Str::lower($input['email']);
        }
        foreach (['document_type', 'document_number', 'tax_id'] as $identifier) {
            if (isset($input[$identifier]) && is_string($input[$identifier])) {
                $input[$identifier] = Str::upper(trim($input[$identifier]));
            }
        }

        $validated = Validator::make($input, [
            'person_type' => ['required', Rule::in(['natural', 'juridica'])],
            'first_name' => ['exclude_unless:person_type,natural', 'required', 'string', 'max:50'],
            'middle_name' => ['exclude_unless:person_type,natural', 'nullable', 'string', 'max:50'],
            'first_surname' => ['exclude_unless:person_type,natural', 'required', 'string', 'max:50'],
            'second_surname' => ['exclude_unless:person_type,natural', 'nullable', 'string', 'max:50'],
            'document_type' => ['exclude_unless:person_type,natural', 'required', Rule::in(['TI', 'CC', 'CE'])],
            'document_number' => ['exclude_unless:person_type,natural', 'required', 'regex:/^[A-Za-z0-9.\-]{4,50}$/', Rule::unique('users', 'document_number')],
            'tax_id' => ['exclude_unless:person_type,juridica', 'required', 'regex:/^[0-9]{5,15}$/', Rule::unique('users', 'tax_id')],
            'legal_name' => ['exclude_unless:person_type,juridica', 'required', 'string', 'max:180'],
            'representative_name' => ['exclude_unless:person_type,juridica', 'required', 'string', 'max:180'],
            'commercial_name' => ['exclude_unless:person_type,juridica', 'required', 'string', 'max:180'],
            'phone' => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
            'phone_secondary' => ['nullable', 'regex:/^\+?[0-9]{7,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'department' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:254', Rule::unique('users', 'email'), function ($attribute, $value, $fail) {
                if (User::whereRaw('LOWER(email) = ?', [Str::lower($value)])->exists()) {
                    $fail('Ese correo ya está registrado.');
                }
            }],
            'terms_accepted' => ['accepted'],
            'password' => $this->passwordRules(),
        ], [
            'required' => 'Este campo es obligatorio.',
            'string' => 'El valor indicado no es válido.',
            'max' => 'El valor supera la longitud permitida.',
            'email' => 'Escribe un correo electrónico válido.',
            'unique' => 'Este dato ya está registrado.',
            'regex' => 'Revisa el formato de este dato.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres.',
        ])->validate();

        $fullName = $validated['person_type'] === 'natural'
            ? implode(' ', array_filter([
                $validated['first_name'],
                $validated['middle_name'] ?? null,
                $validated['first_surname'],
                $validated['second_surname'] ?? null,
            ]))
            : $validated['legal_name'];

        $validated['name'] = $fullName;
        $validated['username'] = $this->uniqueUsername($fullName);
        $validated['role'] = 'cliente';
        $validated['terms_accepted_at'] = now();
        $validated['terms_version'] = '2026-10-06';
        unset($validated['terms_accepted']);

        return User::create($validated);
    }

    private function uniqueUsername(string $name): string
    {
        $base = Str::lower(Str::substr(Str::slug($name), 0, 40));
        $base = $base !== '' ? $base : 'usuario';
        $candidate = $base;
        $suffix = 1;

        while (User::whereRaw('LOWER(username) = ?', [$candidate])->exists()) {
            $candidate = $base.'_'.(++$suffix);
        }

        return $candidate;
    }
}
