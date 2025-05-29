<?php

namespace App\Sys\Res\Atr;

use App\Models\Attribute;

interface IAttribute
{


    public function getAttributeObject() : ?Attribute;

}
