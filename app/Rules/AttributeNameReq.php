<?php

namespace App\Rules;

use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\ElementType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class AttributeNameReq extends ResourceNameReq
{
    const MAX_NAME_LENGTH = 60;

    public function __construct(protected ?ElementType $element_type,protected ?Attribute $attribute){}


    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        parent::validate($attribute,$value,$fail);

        //see if type name is unique for the type
        $laravel = Attribute::where('owner_element_type_id',$this->element_type->id)->where('type_name',$value);
        if ($this->attribute?->id) {
            $laravel->whereNot('id',$this->attribute->id);
        }
        $here = $laravel->first();
        if ($here) {
            $fail('msg.attribute_unique_name_per_type')->translate(['resource_name'=>$value]);
        }


    }

}
