<?php

namespace App\Helpers\Attributes;

use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeOptions
{


    public $is_constant;
    public $is_static;
    public $is_final;
    public $is_human;
    public $is_read_policy_all;
    public $is_write_policy_all;

    public function __construct(Request $request)
    {
        //todo implement
    }

    public function assign(Attribute $attribute) {

    }


}
