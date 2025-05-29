<?php

namespace App\Rules;


use App\Models\ElementType;
use App\Models\UserNamespace;
use Closure;

class ElementTypeNameReq extends ResourceNameReq
{
    const MAX_NAME_LENGTH = 60;

    public function __construct(protected ?ElementType $element_type,protected ?UserNamespace $current_namespace){}


    /**
     * Run the validation rule.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        parent::validate($attribute,$value,$fail);

        $working_namespace = $this->current_namespace;
        if ($this->element_type && !$working_namespace) { $working_namespace = $this->element_type->owner_namespace;}
        if (!$working_namespace) {
            throw new \LogicException("Need a namespace passed in here");
        }
        //see if type name is unique for the namespace
        $laravel = ElementType::where('owner_namespace_id',$working_namespace->id)->where('type_name',$value);
        if ($this->element_type) {
            $laravel->whereNot('id',$this->element_type->id);
        }
        $here = $laravel->first();
        if ($here) {
            $fail('msg.unique_resource_name_per_user')->translate(['resource_name'=>$value]);
        }


    }

}
