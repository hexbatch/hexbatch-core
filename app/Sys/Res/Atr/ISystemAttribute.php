<?php

namespace App\Sys\Res\Atr;

use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\ISystemType;

interface ISystemAttribute extends ISystemResource,IAttribute
{

    public static function getName() :string;
    public static function getClassOwningSystemType() :string|ISystemType;
    public static function getClassParentSystemAttribute() :string|ISystemAttribute;
    public static function getChainName() :string;

    public function getOwningSystemType() : ?ISystemType;



    public function getSystemParent() : ?ISystemAttribute;
    public function getSystemHandle() : ?ISystemAttribute;

    public function isFinal() : bool;
    public function isSeenChildrenTypes() : bool;
}
