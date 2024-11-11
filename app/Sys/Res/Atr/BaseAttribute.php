<?php

namespace App\Sys\Res\Atr;


use App\Api\Cmd\Design\PromoteAttribute\APSetupForSystem;
use App\Api\Cmd\Type\AttributeAddHandle\AttributeHandleForSystem;
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
    protected ?Attribute $attribute = null;

    const UUID = '';
    const TYPE_CLASS = '';
    const PARENT_ATTRIBUTE_CLASS = '';
    const HANDLE_ATTRIBUTE_CLASS = '';
    const ATTRIBUTE_NAME = '';

    const IS_FINAL = false;
    const IS_ABSTRACT = false;
    const IS_SEEN_BY_CHILDREN_TYPES = false;

    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }

     public static function getClassUuid() : string {
         return static::UUID;
     }

     public function getAttributeName(): string {
         return static::ATTRIBUTE_NAME;
     }


     public static function getClassOwningSystemType() :string|ISystemType|null {
         return SystemTypes::getAttributeOwner(static::class);
     }

    public static function getClassParentSystemAttribute() :string|ISystemAttribute {
         return static::PARENT_ATTRIBUTE_CLASS;
     }

     public static function getClassName() :string { return static::ATTRIBUTE_NAME; }

    public static function getDictionaryObject() :ISystemAttribute {
        return SystemAttributes::getAttributeByUuid(static::class);
    }

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
        if (!static::PARENT_ATTRIBUTE_CLASS) {return static::getClassName();}

        $names = [];

         /**
          * @var ISystemAttribute[] $rev
          */
        $rev = array_reverse(static::getParentClasses());

        foreach ($rev as $parent_class) {
            $names[] = $parent_class::getClassName();
        }
        $names[] = static::getClassName();
        return implode(UserNamespace::NAMESPACE_SEPERATOR,$names);
     }

    public function getAttribute() : Attribute {
        if ($this->attribute) {return $this->attribute;}
        $maybe = Attribute::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe) {
            $this->attribute = $maybe;
        } else {
            $this->attribute = $this->makeAttribute();
        }
        return $this->attribute;
    }

    public function makeAttribute() :Attribute
   {
       if ($this->attribute) {return $this->attribute;}
       try {
           $sys_params = new APSetupForSystem();
           $sys_params
               ->setUuid(static::getClassUuid())
               ->setAttributeName(static::getAttributeName())
               ->setDesignAttributeId(null)
               ->setFinal(static::IS_FINAL)
               ->setAbstract(static::IS_ABSTRACT)
               ->setSeenByChild(static::IS_SEEN_BY_CHILDREN_TYPES)
               ->setAttributeApproval(TypeOfApproval::PUBLISHING_APPROVED)
               ->setSystem(true);

           if(static::getClassOwningSystemType()) {
               $sys_params->setOwnerElementTypeId(static::getClassOwningSystemType()::getDictionaryObject()->getTypeObject()->id);
           } else {
               throw new \LogicException("Attribute ". static::class . " Does not have a type");
           }

           if(static::getClassParentSystemAttribute()) {
               $sys_params->setParentAttributeId(static::getClassParentSystemAttribute()::getDictionaryObject()->getAttributeObject()->id);
           }


           $what =  $sys_params->doParamsAndResponse();
           $this->b_did_create_model = true;
           return $what;
       } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }


    public function getAttributeObject() : ?Attribute {
        return $this->getAttribute();
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
        if (!$this->b_did_create_model) {return;}
        if (!static::getSystemHandle()) {return;}

        try
        {
            $sys_params = new AttributeHandleForSystem();
            $sys_params
                ->setAttributeIds([$this->getAttributeObject()->id])
                ->setHandleAttributeId(static::getSystemHandle()->getAttributeObject()->id);
            $sys_params->doParamsAndResponse();

        } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }
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
