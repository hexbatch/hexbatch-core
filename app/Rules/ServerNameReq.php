<?php

namespace App\Rules;

use Closure;

class ServerNameReq extends UserNameReq
{
    const MAX_NAME_LENGTH = 30;
    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        parent::validate($attribute,$value,$fail);

        if (in_array(mb_strtolower($value),static::RESERVED_NAMES) )  {
            $fail('auth.not_reserved_word')->translate(['word'=>$value]);
        }
    }

    const RESERVED_NAMES = [

    ];

}
