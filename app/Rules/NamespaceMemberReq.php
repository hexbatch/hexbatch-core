<?php

namespace App\Rules;

use App\Helpers\Utilities;
use App\Models\UserNamespace;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NamespaceMemberReq implements ValidationRule
{
    protected ?UserNamespace $processed_namespace = null;
    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {return;}
        if ($value instanceof UserNamespace) {
            $this->processed_namespace = $value;
        } else {
            $this->processed_namespace = UserNamespace::resolveNamespace(value: $value);
        }
        if (!$this->processed_namespace->isUserMember(Utilities::getTypeCastedAuthUser())  ) {
            $fail("msg.namespace_not_member")->translate(['ref'=>$this->processed_namespace->getName()]);
        }


    }

}
