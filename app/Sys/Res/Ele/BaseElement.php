<?php

namespace App\Sys\Res\Ele;



use App\Api\Cmd\Element\Promote\EleForSystem;
use App\Api\Cmd\Element\Promote\ElementPromoteParams;
use App\Api\Cmd\Element\Promote\ElementPromoteResponse;
use App\Exceptions\HexbatchInitException;
use App\Models\Element;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\IType;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\ElementPromote;
use App\Sys\Res\Types\Stk\Root\NS\ThisServer\ThisServerNS;


class BaseElement implements ISystemElement
{
    protected ?Element $element;



    const UUID = '';
    const TYPE_CLASS = '';
    const PHASE_CLASS = '';
    const NAMESPACE_CLASS = ThisServerNS::class;

    public static function getClassUuid() : string {
        return static::UUID;
    }



    public function makeElement() :Element
   {
       try
       {
           $sys_params = new EleForSystem;
           $sys_params
               ->setDestinationSetIds([ElementPromoteParams::NO_SETS_MADE_YET_STUB_ID])
               ->setNsOwnerIds([static::getSystemNamespaceClass()->getNamespaceObject()->id])
               ->setParentTypeId(static::getSystemType()->getTypeObject()->id)
               ->setPhaseId(null)
               ->setNumberPerSet(1)
               ->setUuids([static::getClassUuid()])

           ;


           /**
            * @var ElementPromoteParams $promo_params
            */
           $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,ElementPromote::getClassUuid());
           $promo_params->fromCollection($sys_params->makeCollection());

           /**
            * @type ElementPromoteResponse $promo_work
            */
           $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,ElementPromote::getClassUuid());

           /** @var ElementPromoteResponse $promo_results */
           $promo_results = $promo_work::doWork($promo_params);
           return $promo_results->getGeneratedElements()[0];

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
        //todo make sure that all the elements have their phase, and destination set
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
