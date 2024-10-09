<?php

namespace App\Helpers\Admin;

use App\Models\Attribute;
use App\Models\Element;

class SetCreator
{
    protected Element $element_set;

    public static function makeSetCreator(string $element_guid) : SetCreator {
        return new SetCreator();
    }

    public function addMember(Element $parent) {

    }

    public function addAttribute(Attribute $attribute) {

    }

    public function getSet() : Element {
        return $this->element_set;
    }
}
