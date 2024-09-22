<?php

namespace App\Helpers\Attributes;

use App\Models\Attribute;
use App\Models\ElementType;
use Illuminate\Http\Request;

class AttributeGathering
{
    public ?ElementType $parent_element_type = null;

    public ?Attribute $current_attribute = null;
    public function __construct(Request $request,?Attribute $parent_attribute = null, ElementType $parent_element_type = null)
    {

    }

    public function assign() : Attribute {

        return $this->current_attribute;
    }
}
