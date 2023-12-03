<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\UserNameReq;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => [ 'string', 'max:255'],
            'username'=>['required','string','max:30',Rule::unique(User::class),new UserNameReq],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name']??null,
            'username' => $input['username'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
