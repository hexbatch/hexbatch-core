<?php

namespace App\Sys\Res\Types;

use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Servers\ISystemServer;

interface ISystemType extends ISystemResource, IType
{

    public static function getName() :string;

    public static function getParentNameTree() :array;

    public static function getFlatInheritance() : string;

    public function isFinal() : bool;

    /** @return ISystemType[] */
    public function getParentTypes() :array;


    /** @return ISystemAttribute[]|string[] */
    public static function getAttributeClasses() :array;

    /** @return ISystemAttribute[] */
    public function getAttributes() :array;

    public function getTypeNamespace() : ?ISystemNamespace;
    public function getDescriptionElement() : ?ISystemElement;
    public function getServer() : ?ISystemServer;

}
