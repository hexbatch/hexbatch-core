<?php

namespace App\Sys\Res\Ele;



use App\Api\Cmd\Element\Promote\EleForSystem;
use App\Api\Cmd\Element\Promote\ElementPromoteParams;
use App\Api\Cmd\Element\PromoteEdit\EditEleForSystem;
use App\Exceptions\HexbatchInitException;
use App\Models\Element;

use App\Models\Phase;
use App\Sys\Collections\SystemElements;

use App\Sys\Res\ISystemResource;

use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Types\ISystemType;



class BaseElement implements ISystemElement
{
    protected ?Element $element = null;


    const UUID = '';
    const TYPE_CLASS = '';
    const NAMESPACE_CLASS = ThisNamespace::class;

    public function getISystemElement() : ISystemElement { return $this;}

    protected bool $b_did_create_model = false;

    public function didCreateModel(): bool
    {
        return $this->b_did_create_model;
    }

    public static function getClassName(): string
    {
        return 'Element ' . static::getSystemTypeClass()::getClassName();
    }

    public static function getClassUuid(): string
    {
        return static::UUID;
    }

    public function getElement(): Element
    {
        if ($this->element) {
            return $this->element;
        }
        $maybe = Element::whereRaw('ref_uuid = ?', static::getClassUuid())->first();
        if ($maybe) {
            $this->element = $maybe;
        } else {
            $this->element = $this->makeElement();
        }
        return $this->element;
    }

    public function makeElement(): Element
    {
        if ($this->element) {
            return $this->element;
        }
        try {
            $sys_params = new EleForSystem;
            $sys_params
                ->setDestinationSetIds([ElementPromoteParams::NO_SETS_MADE_YET_STUB_ID])
                ->setNsOwnerIds([$this->getISystemElement()::getSystemNamespaceClass()::getDictionaryObject()->getNamespaceObject()->id])
                ->setParentTypeId(static::getSystemTypeClass()::getDictionaryObject()->getTypeObject()->id)
                ->setPhaseId(null)
                ->setSystem(true)
                ->setNumberPerSet(1)
                ->setUuids([static::getClassUuid()]);


            $what = $sys_params->doParamsAndResponse();
            $this->b_did_create_model = true;
            return $what;

        } catch (\Exception $e) {
            throw new HexbatchInitException(message: $e->getMessage() . ': code ' . $e->getCode(), prev: $e);
        }
    }

    public function getElementObject(): ?Element
    {
        return $this->getElement();
    }


    public function onCall(): ISystemResource
    {
        $this->getElementObject();
        return $this;
    }

    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {
            return;
        }
        try {
            $sys_params = new EditEleForSystem();
            $sys_params
                ->setElementIds([$this->getElementObject()->id])
                ->setPhaseId(Phase::getDefaultPhase()->id);


            $sys_params->doParamsAndResponse();

        } catch (\Exception $e) {
            throw new HexbatchInitException(message: $e->getMessage() . ': code ' . $e->getCode(), prev: $e);
        }
    }

    public static function getDictionaryObject(): ISystemElement
    {
        return SystemElements::getElementByUuid(static::class);
    }

    public static function getSystemTypeClass(): string|ISystemType
    {
        return static::TYPE_CLASS;
    }

    public static function getSystemNamespaceClass(): string|ISystemNamespace
    {
        return static::NAMESPACE_CLASS;
    }




}
