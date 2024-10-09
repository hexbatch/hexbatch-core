<?php

namespace App\Sys\Res\Elements;

use App\Models\Element;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;

interface ISystemElement extends ISystemResource
{
    public function getElementUuid() :string;

    /** @return ISystemElementValue[] */
    public function getElementValues() :array;

    public function getElementType() :?ISystemType;
    public function getElementOwner() :?ISystemNamespace;

    public function getElementObject() : ?Element;
}
