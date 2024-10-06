<?php

namespace App\System\Resources\Elements;

use App\System\Resources\Namespaces\ISystemNamespace;
use App\System\Resources\Types\ISystemType;

interface ISystemElement
{
    public function getElementUuid() :string;

    /** @return ISystemElementValue[] */
    public function getElementValues() :array;

    public function getElementType() :ISystemType;
    public function getElementOwner() :ISystemNamespace;
}
