<?php

namespace App\Helpers\Admin;

use App\Models\Attribute;
use App\Models\ElementType;

class AttributeCreator
{
    public static function makeAttributeCreator(string $guid,?Attribute $parent = null,?ElementType $owner = null) : AttributeCreator {
        return new AttributeCreator();
    }
    protected Attribute $some_attr;



    public function setValue($value) {

    }


    public function getAttribute() : Attribute {
        return $this->some_attr;
    }
}
