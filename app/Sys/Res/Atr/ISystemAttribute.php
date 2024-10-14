<?php

namespace App\Sys\Res\Atr;

use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\ISystemType;

interface ISystemAttribute extends ISystemResource,IAttribute
{

    public function getOwningSystemType() : ?ISystemType;



    public function getSystemParent() : ?ISystemAttribute;

    public function isFinal() : bool;
    public function isSeenChildrenTypes() : bool;
}
