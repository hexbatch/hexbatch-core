<?php

namespace App\Helpers\Attributes;

use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeValue
{

    public $is_nullable;
    public $value_type;
    public $value_numeric_min;
    public $value_numeric_max;
    public $value_regex;
    public $value_default;

    public function __construct(Request $request)
    {
        //todo implement
    }

    public function assign(Attribute $attribute) {

    }


}
