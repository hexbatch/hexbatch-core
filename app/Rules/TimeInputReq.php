<?php

namespace App\Rules;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeInputReq implements ValidationRule
{
    protected ?Carbon $processed_time = null;
    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->processed_time = null;
        if (empty($value)) {return;}

        try {
            $this->processed_time = Carbon::parse($value);
        } catch (InvalidFormatException $t) {
            $fail("msg.invalid_time_with_message")->translate(['ref'=>$value,'msg'=>$t->getMessage()]);
            return;
        }

    }

}
