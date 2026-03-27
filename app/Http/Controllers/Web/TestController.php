<?php

namespace App\Http\Controllers\Web;



use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\ElementTypeExposedAttribute;
use App\Models\ElementTypeParent;

class TestController
{

    public function test() {

        $t = ElementType::find(390);
        $t->loadMissing('type_exposed_attributes');
        dd($t->type_exposed_attributes->toArray());
    }
}
