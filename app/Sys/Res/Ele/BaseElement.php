<?php

namespace App\Sys\Res\Ele;



use App\Exceptions\HexbatchInitException;
use App\Models\Element;

use App\Models\ElementType;
use App\Models\ElementValue;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Namespaces\ISystemNamespace;

use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\IType;


class BaseElement implements ISystemElement
{
    protected ?Element $element;



    const UUID = '';
    const TYPE_CLASS = '';
    const NAMESPACE_CLASS = '';

    public static function getUuid() : string {
        return static::UUID;
    }



    public function makeElement() :Element
   {
       try {
            $element = new Element();
           return $element;
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }

    public function getElementObject() : ?Element {
        if ($this->element) {return $this->element;}
        $this->element = $this->makeElement();
        return $this->element;
    }


    public function onCall(): ISystemResource
    {
        $this->getElementObject();
        return $this;
    }

    public function onNextStep(): void
    {

    }


    public function getSystemElementValues(): array
    {
        return [];
    }

    public function getSystemType(): ?ISystemType
    {
        return SystemTypes::getTypeByUuid(static::TYPE_CLASS);
    }

    public function getSystemNamespaceOwner(): ?ISystemNamespace
    {
        return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_CLASS);
    }

    public function getElementValue(\App\Sys\Res\Sets\ISet $set): ?ElementValue
    {
        return $this->getElementObject()?->getValueBySet($set->getSetObject());
    }

    public function getElementType(): ?ElementType
    {
        return $this->getElementObject()?->element_parent_type;
    }

    public function getTypeInterface() :?IType {
        return $this->getElementObject()?->element_parent_type;
    }

    public function getNamespaceInterface(): ?INamespace
    {
        return $this->getElementObject()?->element_namespace;
    }
}
