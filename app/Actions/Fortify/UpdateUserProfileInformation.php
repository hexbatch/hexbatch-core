<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\UserNameReq;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;


class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param array<string, string> $input
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {

        Validator::make($input, [
            'name' => [ 'string', 'max:255'],
            'username'=>['required','string','min:3',Rule::unique(User::class,'username')->ignore($user->id),new UserNameReq],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            /** @noinspection PhpParamsInspection */
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name']??null,
                'username' => $input['username'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'username' => $input['name'],
            'email' => $input['email']??null,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
