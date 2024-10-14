<?php

namespace App\Sys\Res\Atr;


use App\Exceptions\HexbatchInitException;
use App\Models\Attribute;
use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\ISystemType;


 abstract class BaseAttribute implements ISystemAttribute
{
    protected ?Attribute $attribute;

    const UUID = '';
    const TYPE_UUID = '';
    const PARENT_UUID = '';

     public function getAttributeName(): string {
         return static::ATTRIBUTE_NAME;
     }

    public function getAttributeUuid() :string { return static::UUID;}

    public function makeAttribute() :Attribute
   {
       try {
           $attribute = new Attribute();
           return $attribute;
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }




    public function getAttributeObject() : ?Attribute {
        if ($this->attribute) {return $this->attribute;}
        $this->attribute = $this->makeAttribute();
        return $this->attribute;
    }



    public function getParent(): ISystemAttribute
    {
        return SystemAttributes::getAttributeByUuid(static::PARENT_UUID);
    }

    public function getOwningType(): ISystemType
    {
        return SystemTypes::getTypeByUuid(static::TYPE_UUID);
    }

    public function onCall(): ISystemResource
    {
        $this->getAttributeObject();
        return $this;
    }

    public function onNextStep(): void
    {

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
         return null;
     }
 }
