<?php

namespace App\Sys\Res\Atr;

use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\ElementValue;

interface IAttribute
{
    public function getAttributeUuid() :string;
    public function getOwningType() : ?ElementType;
    public function getAttributeName() :string;
    public function getStartingElementValue() :?ElementValue;
    public function getAttributeData() :?array;

    public function getAttributeObject() : ?Attribute;

}
