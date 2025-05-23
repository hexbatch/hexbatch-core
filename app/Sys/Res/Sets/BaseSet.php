<?php

namespace App\Sys\Res\Sets;


use App\Exceptions\HexbatchInitException;
use App\Helpers\Utilities;
use App\Models\ElementSet;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemSets;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\SetCreate;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\St\SetMemberAdd;


class BaseSet implements ISystemSet
{
     const UUID = '';

    const ELEMENT_CLASS = '';
    const HAS_EVENTS = true;
    const IS_STICKY = false;

    const CONTAINING_ELEMENT_CLASSES = [

    ];



    protected ?ElementSet $set = null;

    public static function getFullClassName() :string {return static::class;}
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
     public static function isSticky() :bool { return static::IS_STICKY;}

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
           $element_uuids = [];
           foreach ($iset->getSystemElements() as $some_element_class) {
               $element_uuids[] = $some_element_class->getElementObject()?->getUuid();
           }
           array_filter($element_uuids, static function($var){return $var !== null;} );

           $setter = new SetCreate(
               given_element_uuid: $iset->getDefiningSystemElement()->getElementObject()?->getUuid(),
               uuid: static::getClassUuid(),
               set_has_events: static::hasEvents(), is_system: true,send_event: false
           );
           $setter->runAction();

            if (count($element_uuids)) {
                $better = new SetMemberAdd(
                    given_set_uuid: $setter->getCreatedSet()->ref_uuid,
                    given_element_uuids: $element_uuids,
                    is_sticky: static::isSticky(),
                    is_system: true,send_event: false
                );
                $better->runAction();
            }



           $what =  $setter->getCreatedSet();
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

    public function __construct(
        protected bool $b_type_init = false
    ) {

    }


 }
