<?php

namespace App\Sys\Res\Atr;

use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\ISystemType;

interface ISystemAttribute extends ISystemResource,IAttribute
{
    public function getAttributeName() :string;
    public static function getDictionaryObject() :ISystemAttribute;

    public static function getClassOwningSystemType() :string|ISystemType|null;
    public static function getClassParentSystemAttribute() :string|ISystemAttribute;
    public static function getChainName() :string;




    public function getSystemHandle() : ?ISystemAttribute;

    public static function isSystem(): bool;
    public static function isAbstract(): bool;
    public static function isFinal() : bool;
    public static function isSeenChildrenTypes() : bool;


    public function getISystemAttribute() : ISystemAttribute;
}
