<?php

namespace App\Sys\Res\Attributes;

use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\ISystemType;

interface ISystemAttribute extends ISystemResource
{
    public function getAttributeUuid() :string;
    public function getOwningType() : ?ISystemType;
    public function getAttributeName() :string;
    public function getAttributeData() :?array;

    public function getParent() : ?ISystemAttribute;

    public function isFinal() : bool;
    public function isFinalParent() : bool;
}
