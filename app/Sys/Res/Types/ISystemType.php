<?php

namespace App\Sys\Res\Types;

use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Servers\ISystemServer;

interface ISystemType extends ISystemResource, IType
{

    public static function getDictionaryObject() :ISystemType;
    public static function getTypeNamespaceClass() :string|ISystemNamespace;
    public static function getTypeServerClass() :string|ISystemServer;

    public static function getParentNameTree() :array;
    public static function hasInAncestors(string $target_full_class_name) :bool;

    public static function getFlatInheritance() : string;

    public function isFinal() : bool;

    /** @return ISystemType[] */
    public function getParentTypes() :array;


    /** @return ISystemAttribute[]|string[] */
    public static function getAttributeClasses() :array;

    public static function getSystemHandleElementClass() :string|ISystemElement;

    /** @return ISystemAttribute[] */
    public function getAttributes() :array;

    public function getTypeNamespace() : ?ISystemNamespace;
    public function getHandleElement() : ?ISystemElement;
    public function getClassServer() : ?ISystemServer;

}
