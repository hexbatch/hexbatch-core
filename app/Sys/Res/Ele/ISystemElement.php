<?php

namespace App\Sys\Res\Ele;

use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;

interface ISystemElement extends ISystemResource,IElement
{

    public static function getDictionaryObject() :ISystemElement;
    public static function getSystemTypeClass() :string|ISystemType;
    public static function getSystemNamespaceClass() :string|ISystemNamespace;

    public function getISystemElement() : ISystemElement;

}
