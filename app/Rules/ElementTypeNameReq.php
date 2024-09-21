<?php

namespace App\Rules;

use App\Helpers\Utilities;
use App\Models\ElementType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ElementTypeNameReq extends ResourceNameReq
{


    public function __construct(protected ?ElementType $element_type){}


    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        parent::validate($attribute,$value,$fail);

        //see if type name is unique for the user
        $laravel = ElementType::where('user_id',Auth::id())->where('type_name',$value);
        if ($this->element_type) {
            $laravel->whereNot('id',$this->element_type->id);
        }
        $here = $laravel->first();
        if ($here) {
            $fail('msg.unique_resource_name_per_user',['resource_name'=>$value])->translate();
        }


    }

}
