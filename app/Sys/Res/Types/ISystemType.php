<?php

namespace App\Sys\Res\Types;

use App\Models\ElementType;
use App\Sys\Res\Attributes\ISystemAttribute;
use App\Sys\Res\Elements\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Servers\ISystemServer;

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
