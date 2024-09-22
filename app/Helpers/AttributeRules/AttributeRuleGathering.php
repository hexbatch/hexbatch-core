<?php

namespace App\Helpers\AttributeRules;

use App\Models\ElementType;
use Illuminate\Http\Request;

class AttributeRuleGathering
{
    public function __construct(Request $request,?ElementType $element_type = null)
    {

    }
}
