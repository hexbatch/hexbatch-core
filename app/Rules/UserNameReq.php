<?php

namespace App\Rules;

use App\Helpers\Utilities;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserNameReq implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (mb_strtolower($value) === mb_strtolower(User::SYSTEM_NAME) )  {
            $fail('auth.not_reserved_word')->translate();
        }
    }

}
