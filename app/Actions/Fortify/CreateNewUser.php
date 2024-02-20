<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\ResourceNameReq;
use App\Rules\UserNameReq;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    const MAX_USERNAME_LENGTH = 30;
    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     * @throws ValidationException
     */
    public function create(array $input, ?string $server_name = null): User
    {
        $max = static::MAX_USERNAME_LENGTH;
        if ($server_name) {$max = static::MAX_USERNAME_LENGTH * 2 + 1;}
        //The server name is the server username here, so not using domain name in the table
        Validator::make($input, [
            'name' => [ 'string', 'max:255'],
            'username'=>['required','string',"max:$max",'min:3',Rule::unique(User::class),new ResourceNameReq,new UserNameReq],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name']??null,
            'username' => $input['username'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
