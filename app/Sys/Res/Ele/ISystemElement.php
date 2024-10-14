<?php

namespace App\Sys\Res\Ele;

use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;

interface ISystemElement extends ISystemResource,IElement
{

    /** @return ISystemElementValue[] */
    public function getSystemElementValues() :array;

    public function getSystemType() :?ISystemType;
    public function getSystemNamespaceOwner() :?ISystemNamespace;


}
