<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'cnpj_cpf' => ['required', 'string', 'cpf_cnpj'],
            // 'password' => $this->passwordRules(),
            'password' => 'required|string|min:8|confirmed',
            // 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'terms' => 'accepted',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'cnpj_cpf' => $input['cnpj_cpf'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
