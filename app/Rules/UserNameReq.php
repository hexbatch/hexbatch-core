<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserNameReq implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if ((!preg_match('/^[A-Za-z0-9_]+$/', $value) ) || preg_match('/^\d/', $value)) {
            $fail('auth.invalid_name')->translate();
        }
    }

}
