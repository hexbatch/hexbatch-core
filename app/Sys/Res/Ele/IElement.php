<?php

namespace App\Sys\Res\Ele;

use App\Models\Element;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Sets\ISet;
use App\Sys\Res\Types\IType;

interface IElement
{
    public function getElementUuid() :string;

    public function getElementValue(ISet $set) :?ElementValue;

    public function getElementType() :?ElementType;
    public function getTypeInterface() :?IType;
    public function getNamespaceInterface() :?INamespace;

    public function getElementObject() : ?Element;
}
