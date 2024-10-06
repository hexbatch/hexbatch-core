<?php

namespace App\System\Resources\Types;

use App\System\Resources\Attributes\ISystemAttribute;
use App\System\Resources\Elements\ISystemElement;
use App\System\Resources\Namespaces\ISystemNamespace;

interface ISystemType
{
    public function getTypeUuid() :string;
    public function getTypeName() :string;

    public function isFinal() : bool;

    /** @return ISystemType[] */
    public function getParentTypes() :array;

    /** @return ISystemAttribute[] */
    public function getAttributes() :array;

    public function getOwningNamespace() : ISystemNamespace;
    public function getDescriptionElement() : ISystemElement;
}
