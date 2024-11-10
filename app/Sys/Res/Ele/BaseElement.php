<?php

namespace App\Sys\Res\Ele;



use App\Api\Cmd\Element\Promote\EleForSystem;
use App\Api\Cmd\Element\Promote\ElementPromoteParams;
use App\Api\Cmd\Element\PromoteEdit\EditEleForSystem;
use App\Exceptions\HexbatchInitException;
use App\Models\Element;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Models\Phase;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\IType;


class BaseElement implements ISystemElement
{
    protected ?Element $element = null;



    const UUID = '';
    const TYPE_CLASS = '';
    const PHASE_CLASS = '';
    const NAMESPACE_CLASS = ThisNamespace::class;

    public static function getClassUuid() : string {
        return static::UUID;
    }

    public function getElement() : Element {
        if ($this->element) {return $this->element;}
        $maybe = Element::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe) {
            $this->element = $maybe;
        } else {
            $this->element = $this->makeElement();
        }
        return $this->element;
    }

    public function makeElement() :Element
   {
       if ($this->element) {return $this->element;}
       try
       {
           $sys_params = new EleForSystem;
           $sys_params
               ->setDestinationSetIds([ElementPromoteParams::NO_SETS_MADE_YET_STUB_ID])
               ->setNsOwnerIds([static::getSystemNamespaceClass()::getDictionaryObject()->getNamespaceObject()->id])
               ->setParentTypeId(static::getSystemTypeClass()::getDictionaryObject()->getTypeObject()->id)
               ->setPhaseId(null)
               ->setSystem(true)
               ->setNumberPerSet(1)
               ->setUuids([static::getClassUuid()])

           ;


           return $sys_params->doParamsAndResponse();

       } catch (\Exception $e) {
           throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }

    public function getElementObject() : ?Element {
        return $this->getElement();
    }


    public function onCall(): ISystemResource
    {
        $this->getElementObject();
        return $this;
    }

    public function onNextStep(): void
    {
        try
        {
            $sys_params = new EditEleForSystem();
            $sys_params
                ->setElementIds([$this->getElementObject()->id])
                ->setPhaseId(Phase::getDefaultPhase()->id)
            ;


            $sys_params->doParamsAndResponse();

        } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }
    }
    public static function getDictionaryObject() :ISystemElement {
        return SystemElements::getElementByUuid(static::class);
    }

    public static function getSystemTypeClass() :string|ISystemType {
        return static::TYPE_CLASS;
    }

    public static function getPhaseSystemTypeClass() :string|ISystemType {
        return static::PHASE_CLASS;
    }

    public static function getSystemNamespaceClass() :string|ISystemNamespace {
        return static::NAMESPACE_CLASS;
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
