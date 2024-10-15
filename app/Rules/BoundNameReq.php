<?php

namespace App\Rules;


use Closure;


class BoundNameReq extends ResourceNameReq
{
    const MAX_NAME_LENGTH = 128;

    public function __construct(){}


    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        parent::validate($attribute,$value,$fail);
    }

}
