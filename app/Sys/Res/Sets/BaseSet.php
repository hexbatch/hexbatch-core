<?php

namespace App\Sys\Res\Sets;


use App\Api\Cmd\Set\Promote\SetForSystem;
use App\Exceptions\HexbatchInitException;
use App\Helpers\Utilities;
use App\Models\ElementSet;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemSets;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;



 class BaseSet implements ISystemSet
{
     const UUID = '';

    const ELEMENT_CLASS = '';
    const HAS_EVENTS = true;

    const CONTAINING_ELEMENT_CLASSES = [

    ];



    protected ?ElementSet $set = null;
     public function getISystemSet() : ISystemSet {return $this;}
     protected bool $b_did_create_model = false;
     public function didCreateModel(): bool { return $this->b_did_create_model; }

     public static function getClassUuid() : string {
         return static::UUID;
     }

     public static function getClassName() :string {
         return 'Set defined by '. static::getDefiningSystemElementClass()::getClassName();
     }

     public static function getDictionaryObject() :ISystemSet {
         return SystemSets::getSetByUuid(static::class);
     }

     public static function hasEvents() :bool { return static::HAS_EVENTS;}

     public static function getDefiningSystemElementClass() :string|ISystemElement {
         return static::ELEMENT_CLASS;
     }

     /** @return ISystemElement[]|string[] */
     public static function getMemberSystemElementClasses() :array {
         return static::CONTAINING_ELEMENT_CLASSES;
     }



     public static function getDefiningSystemElement(): ?ISystemElement
     {
         return SystemElements::getElementByUuid(static::getDefiningSystemElementClass());
     }

     public function getSystemElements(): array
     {
         $ret = [];
         foreach (static::CONTAINING_ELEMENT_CLASSES as $el_uuid) {
             $ret[] = SystemElements::getElementByUuid($el_uuid);
         }
         return $ret;
     }

     public function getSet() : ElementSet {
         if ($this->set) {return $this->set;}
         $maybe = ElementSet::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
         if ($maybe) {
             $this->set = $maybe;
         } else {
             $this->set = $this->makeSet();
         }
         return $this->set;
     }
     /**
      * @return ElementSet
      */
    public function makeSet() :ElementSet
   {
       if ($this->set) {return $this->set;}
       try
       {
           $iset = $this->getISystemSet();
           $element_ids = [];
           foreach ($iset->getSystemElements() as $some_element_class) {
               $element_ids[] = $some_element_class->getElementObject()?->id;
           }
           $sys_params = new SetForSystem();
           $sys_params
               ->setUuid(static::getClassUuid())
               ->setParentSetElementId($iset->getDefiningSystemElement()->getElementObject()?->id)
               ->setHasEvents(static::hasEvents())
               ->setSystem(true)
               ->setContentElementIds($element_ids)
              ;

           $what =  $sys_params->doParamsAndResponse();
           $this->b_did_create_model = true;
           return $what;

       } catch (\Exception $e) {
           throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }


    public function getSetObject() : ?ElementSet {
        return $this->getSet();
    }

    public function onCall(): ISystemResource
    {
        $this->getSetObject();
        return $this;
    }

    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        Utilities::ignoreVar(); //for linting
    }




 }
