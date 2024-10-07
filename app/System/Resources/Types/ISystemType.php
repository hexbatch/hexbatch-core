<?php

namespace App\System\Resources\Types;

use App\Models\ElementType;
use App\System\Resources\Attributes\ISystemAttribute;
use App\System\Resources\Elements\ISystemElement;
use App\System\Resources\ISystemResource;
use App\System\Resources\Namespaces\ISystemNamespace;
use App\System\Resources\Servers\ISystemServer;

interface ISystemType extends ISystemResource
{
    public function getTypeUuid() :string;
    public function getTypeName() :string;

    public function isFinal() : bool;

    /** @return ISystemType[] */
    public function getParentTypes() :array;

    /** @return ISystemAttribute[] */
    public function getAttributes() :array;

    public function getTypeNamespace() : ?ISystemNamespace;
    public function getDescriptionElement() : ?ISystemElement;
    public function getServer() : ?ISystemServer;

    public function getTypeObject() : ?ElementType;
}
