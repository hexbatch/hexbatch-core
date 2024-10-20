<?php

namespace App\Sys\Res\Types;


use App\Exceptions\HexbatchInitException;
use App\Models\ElementType;
use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Servers\Stock\ThisServer;

abstract class BaseType implements ISystemType
{
    protected ?ElementType $type;

    const UUID = '';
    const NAMESPACE_UUID = '';
    const DESCRIPTION_ELEMENT_UUID = '';
    const SERVER_UUID = ThisServer::UUID;

    const TYPE_NAME = '';

    const ATTRIBUTE_UUIDS = [];

    const PARENT_UUIDS = [];

    public function getUuid() : string {
        return static::UUID;
    }

    public function getTypeUuid() :string { return static::UUID;}

    public function getServer() : ?ISystemServer {
        return SystemServers::getServerByUuid(static::SERVER_UUID);
    }

    public function makeType() :ElementType
   {
       try {
           $type = new ElementType();
           return $type;
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }

    /** @return ISystemAttribute[] */
    public function getAttributes() :array {
        $ret = [];
        foreach (static::ATTRIBUTE_UUIDS as $uuid) {
            $ret[] = SystemAttributes::getAttributeByUuid($uuid);
        }
        return $ret;
    }

    /** @return ISystemType[] */
    public function getParentTypes() :array {
        $ret = [];
        foreach (static::ATTRIBUTE_UUIDS as $uuid) {
            $ret[] = SystemTypes::getTypeByUuid($uuid);
        }
        return $ret;
    }

    public function getDescriptionElement(): ?ISystemElement
    {
        return SystemElements::getElementByUuid(static::DESCRIPTION_ELEMENT_UUID);
    }



    public function getTypeObject() : ?ElementType {
        if ($this->type) {return $this->type;}
        $this->type = $this->makeType();
        return $this->type;
    }

    public function getTypeNamespace() :?ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_UUID);
    }

    public function getTypeName(): string { return static::TYPE_NAME;}

    public function isFinal(): bool { return false; }

    public function onCall(): ISystemResource
    {
        $this->getTypeObject();
        return $this;
    }

    public function onNextStep(): void
    {
        //users add in the default namespace using the uuid of the now generated ns
        $ns = $this->getTypeNamespace();
        if (!$ns) {
            throw new HexbatchInitException('type next step cannot get ns');
        }
        $this->getTypeObject()->owner_namespace_id = $ns->getNamespaceObject()->id;
        $this->getTypeObject()->save();
    }



}
