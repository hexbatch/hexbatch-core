<?php

namespace App\Rules;

use App\Helpers\Utilities;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ResourceNameReq implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (!preg_match('/^\p{L}[\p{L}0-9_]{2,}$/', $value) ) {
            $fail('auth.invalid_name')->translate();
        }

        if (Utilities::is_uuid_similar($value) ) {
            $fail('auth.not_uuid_name')->translate();
        }

        if(mb_strtolower($value) !== $value) {
            $fail('auth.not_upper_case_name')->translate();
        }
        if (Utilities::positiveBoolWords($value) || Utilities::negativeBoolWords($value)) {
            $fail('auth.not_reserved_word')->translate();
        }
    }

}
