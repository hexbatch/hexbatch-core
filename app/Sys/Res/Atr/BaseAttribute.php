<?php

namespace App\Sys\Res\Atr;


use App\Api\Cmd\Design\PromoteAttribute\APSetupForSystem;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchInitException;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\ElementValue;
use App\Models\UserNamespace;
use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\ISystemType;


abstract class BaseAttribute implements ISystemAttribute
{
    protected ?Attribute $attribute;

    const UUID = '';
    const TYPE_CLASS = '';
    const PARENT_ATTRIBUTE_CLASS = '';
    const HANDLE_ATTRIBUTE_CLASS = '';
    const ATTRIBUTE_NAME = '';

    const IS_FINAL = false;
    const IS_ABSTRACT = false;
    const IS_SEEN_BY_CHILDREN_TYPES = false;

     public static function getClassUuid() : string {
         return static::UUID;
     }

     public function getAttributeName(): string {
         return static::ATTRIBUTE_NAME;
     }

     public static function getClassAttributeName(): string {
         return static::ATTRIBUTE_NAME;
     }

     public static function getClassOwningSystemType() :string|ISystemType {
         return static::TYPE_CLASS;
     }

    public static function getClassParentSystemAttribute() :string|ISystemAttribute {
         return static::PARENT_ATTRIBUTE_CLASS;
     }

     public static function getName() :string { return static::ATTRIBUTE_NAME; }

     public static function getParentClasses() :array  {
         $ret = [];
         /**
          * @type ISystemAttribute $me
          */
         $me = static::class;
         while($me && $parent_class = $me::PARENT_ATTRIBUTE_CLASS) {
             $interfaces = class_implements($parent_class);
             if (isset($interfaces['App\Sys\Res\Atr\ISystemAttribute'])) {
                 $me = $parent_class;
                 $ret[] = $me;
             } else {
                 throw new \LogicException("Parent $parent_class is not an attribute for ".static::class);
             }

         }
         return $ret;
     }
     public static function getChainName() :string {
        if (!static::PARENT_ATTRIBUTE_CLASS) {return static::getName();}

        $names = [];

         /**
          * @var ISystemAttribute[] $rev
          */
        $rev = array_reverse(static::getParentClasses());

        foreach ($rev as $parent_class) {
            $names[] = $parent_class::getName();
        }
        $names[] = static::getName();
        return implode(UserNamespace::NAMESPACE_SEPERATOR,$names);
     }


    public function makeAttribute() :Attribute
   {
       if ($this->attribute) {return $this->attribute;}
       try {
           $sys_params = new APSetupForSystem();
           $sys_params
               ->setUuid(static::getClassUuid())
               ->setAttributeName(static::getClassAttributeName())
               ->setOwnerElementTypeId(static::getClassOwningSystemType())
               ->setParentAttributeId(static::getClassParentSystemAttribute())
               ->setDesignAttributeId(null)
               ->setFinal(static::IS_FINAL)
               ->setAbstract(static::IS_ABSTRACT)
               ->setSeenByChild(static::IS_SEEN_BY_CHILDREN_TYPES)
               ->setAttributeApproval(TypeOfApproval::PUBLISHING_APPROVED)
               ->setSystem(true);

           return $sys_params->doParamsAndResponse();
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }




    public function getAttributeObject() : ?Attribute {
        if ($this->attribute) {return $this->attribute;}
        $this->attribute = $this->makeAttribute();
        return $this->attribute;
    }



    public function getSystemParent(): ISystemAttribute
    {
        return SystemAttributes::getAttributeByUuid(static::PARENT_ATTRIBUTE_CLASS);
    }

    public function getSystemHandle() : ?ISystemAttribute
    {
        return SystemAttributes::getAttributeByUuid(static::HANDLE_ATTRIBUTE_CLASS);
    }

    public function getOwningSystemType(): ISystemType
    {
        return SystemTypes::getTypeByUuid(static::TYPE_CLASS);
    }

    public function onCall(): ISystemResource
    {
        $this->getAttributeObject();
        return $this;
    }

    public function onNextStep(): void
    {
        //todo set the design here, if present
    }


    public function isFinal(): bool
    {
        return false;
    }

    public function isSeenChildrenTypes(): bool
    {
        return false;
    }


     public function getAttributeData(): ?array
     {
         return $this->getStartingElementValue()?->element_value?->getArrayCopy();
     }

     public function getStartingElementValue() :?ElementValue {
         return $this->getAttributeObject()?->original_element_value;
     }

     public function getOwningType() : ?ElementType {
         return $this->getAttributeObject()->type_owner;
     }
 }
