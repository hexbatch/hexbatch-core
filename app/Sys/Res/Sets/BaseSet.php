<?php

namespace App\Sys\Res\Sets;


use App\Exceptions\HexbatchInitException;
use App\Models\ElementSet;
use App\Sys\Collections\SystemElements;
use App\Sys\Res\Elements\ISystemElement;
use App\Sys\Res\ISystemResource;



 class BaseSet implements ISystemSet
{
    const UUID = '';
    const ELEMENT_UUID = '';

    const CONTAINING_ELEMENT_UUIDS = [

    ];

    protected ?ElementSet $set;


     public function getSetUuid(): string { return static::UUID;}



     public function hasEvents(): bool
     {
         return true;
     }

     public function getDefiningElement(): ?ISystemElement
     {
         return SystemElements::getElementByUuid(static::ELEMENT_UUID);
     }

     public function getElements(): array
     {
         $ret = [];
         foreach (static::CONTAINING_ELEMENT_UUIDS as $el_uuid) {
             $ret[] = SystemElements::getElementByUuid($el_uuid);
         }
         return $ret;
     }


    public function makeSet() :ElementSet
   {
       try {
           $set = new ElementSet();
           return $set;
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }


    public function getSetObject() : ?ElementSet {
        if ($this->set) {return $this->set;}
        $this->set = $this->makeSet();
        return $this->set;
    }

    public function onCall(): ISystemResource
    {
        $this->getSetObject();
        return $this;
    }

    public function onNextStep(): void
    {

    }




 }
