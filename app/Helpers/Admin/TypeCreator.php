<?php

namespace App\Helpers\Admin;

use App\Models\Attribute;
use App\Models\ElementType;

class TypeCreator
{
    public static function makeTypeCreator(string $guid) : TypeCreator {
        return new TypeCreator();
    }
    protected ElementType $da_type;


    public function addParent(ElementType $parent) {

    }

    public function addAttribute(Attribute $attribute) {

    }

    public function publish() {

    }

    public function getType() : ElementType {
        return $this->da_type;
    }
}
