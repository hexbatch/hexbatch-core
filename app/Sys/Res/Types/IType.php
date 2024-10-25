<?php

namespace App\Sys\Res\Types;

use App\Models\ElementType;

interface IType
{
    public function getTypeName() :string;

    public function getTypeObject() : ?ElementType;
}
