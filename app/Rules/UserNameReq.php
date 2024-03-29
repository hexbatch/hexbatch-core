<?php

namespace App\Rules;

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

        if (in_array(mb_strtolower($value),static::RESERVED_NAMES) )  {
            $fail('auth.not_reserved_word',['word'=>$value])->translate();
        }
    }

    const RESERVED_NAMES = [

    ];

}
