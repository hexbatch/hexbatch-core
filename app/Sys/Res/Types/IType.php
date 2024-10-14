<?php

namespace App\Sys\Res\Types;

use App\Models\ElementType;

interface IType
{
    public function getTypeUuid() :string;
    public function getTypeName() :string;

    public function getTypeObject() : ?ElementType;
}
