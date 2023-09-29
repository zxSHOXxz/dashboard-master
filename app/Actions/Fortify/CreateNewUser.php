<?php

namespace App\Actions\Fortify;

use App\Models\Client;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Validation\Rule;

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
        $guard = config('fortify.guard');

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique(User::class)],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique($guard === 'web' ? User::class : ($guard === 'client' ? Client::class : null)),
            ],
            'password' => $this->passwordRules(),
            // 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',

        ])->validate();
        if ($guard === 'donor') {
            $client = new Donor();
            $client->name = $input['name'];
            $client->email = $input['email'];
            $client->password = Hash::make($input['password']);
            $client->save();
        }
        return $client;
    }
}
