<?php

namespace App\System\Resources\Attributes;

use App\System\Resources\ISystemResource;
use App\System\Resources\Types\ISystemType;

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
