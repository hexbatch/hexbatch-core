<?php

namespace App\Helpers\Admin;

use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementType;


class ElementCreator
{
    protected Element $element;

    public static function makeElementCreator(?string $element_guid = null,?ElementType $type = null) : ElementCreator {
        return new ElementCreator();
    }

    public function setValue(Attribute $attribute) {

    }


    public function getElement() : Element {
        return $this->element;
    }
}
