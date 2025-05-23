<?php

namespace App\Sys\Res\Ele;



use App\Exceptions\HexbatchInitException;
use App\Models\Element;

use App\Models\Phase;
use App\Sys\Collections\SystemElements;

use App\Sys\Res\ISystemResource;

use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementEdit;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\ElementCreate;


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

    public static function getFullClassName() :string {return static::class;}

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
            $creator = new ElementCreate(
                given_type_uuid:static::getSystemTypeClass()::getDictionaryObject()->getTypeObject()->getUuid(),
                given_namespace_uuid: $this->getISystemElement()::getSystemNamespaceClass()::getDictionaryObject()->getNamespaceObject()->getUuid(),
                given_phase_uuid: null,
                given_set_uuids: [],
                number_to_create: 1,
                preassinged_uuids: [static::getClassUuid()],
                is_system: true, send_event: false
            );

            $creator->runAction();
            if (!count($creator->getElementsCreated())) { throw new \LogicException("Could not create element ".static::getClassUuid());}
            $what = $creator->getElementsCreated()[0];
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
            $changer = new ElementEdit(given_element_uuid: $this->getElementObject()->getUuid(),
                change_phase_uuid: Phase::getDefaultPhase()->ref_uuid,is_system: true);

            $changer->runAction();

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

    public function __construct(
        protected bool $b_type_init = false
    ) {

    }


}
