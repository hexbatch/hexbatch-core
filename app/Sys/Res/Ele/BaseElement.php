<?php

namespace App\Sys\Res\Ele;



use App\Exceptions\HexbatchInitException;
use App\Models\Element;

use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;

use App\Sys\Res\Types\ISystemType;


class BaseElement implements ISystemElement
{
    protected ?Element $element;

    const UUID = '';
    const TYPE_UUID = '';
    const NAMESPACE_UUID = '';

    public function getElementUuid() :string { return static::UUID;}

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


    public function getElementValues(): array
    {
        return [];
    }

    public function getElementType(): ?ISystemType
    {
        return SystemTypes::getTypeByUuid(static::TYPE_UUID);
    }

    public function getElementOwner(): ?ISystemNamespace
    {
        return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_UUID);
    }
}
