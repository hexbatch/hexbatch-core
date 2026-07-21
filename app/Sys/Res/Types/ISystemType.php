<?php

namespace App\Sys\Res\Types;

use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;

interface ISystemType extends ISystemResource
{



    public static function getParentNameTree() :array;
    public static function hasInAncestors(string $target_full_class_name) :bool;

    public static function getFlatInheritance() : string;

    public function isFinal() : bool;



    /** @return ISystemAttribute[]|string[] */
    public static function getAttributeClasses() :array;

    public static function getSystemHandleElementClass() :string|ISystemElement;

    /** @return ISystemAttribute[]
     * @noinspection PhpUnused
     */
    public function getAttributes() :array;



    public function getISystemType() : ?ISystemType;

}
