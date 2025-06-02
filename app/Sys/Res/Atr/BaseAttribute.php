<?php

namespace App\Sys\Res\Atr;


use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Exceptions\HexbatchInitException;
use App\Models\Attribute;
use App\Models\UserNamespace;
use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\DocumentTrait;
use App\Sys\Res\IDocument;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignAttributeCreate;


abstract class BaseAttribute implements ISystemAttribute, IDocument
{
    use DocumentTrait;

    protected ?Attribute $attribute = null;

    const UUID = '';
    const PARENT_ATTRIBUTE_CLASS = '';
    const HANDLE_ATTRIBUTE_CLASS = '';
    const ATTRIBUTE_NAME = '';

    const IS_FINAL = false;
    const IS_ABSTRACT = false;
    const IS_SEEN_BY_CHILDREN_TYPES = false;

    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }

    public function getISystemAttribute() : ISystemAttribute {
        return $this;
    }

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

     public static function getHexbatchClassName() :string { return static::ATTRIBUTE_NAME; }

    public static function getFullClassName() :string {return static::class;}

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
        if (!static::PARENT_ATTRIBUTE_CLASS) {return static::getHexbatchClassName();}

        $names = [];

         /**
          * @var ISystemAttribute[] $rev
          */
        $rev = array_reverse(static::getParentClasses());

        foreach ($rev as $parent_class) {
            $names[] = $parent_class::getHexbatchClassName();
        }
        $names[] = static::getHexbatchClassName();
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
           $parent_uuid = null;
           if($this->getISystemAttribute()::getClassParentSystemAttribute()) {
               $parent_uuid = static::getClassParentSystemAttribute()::getDictionaryObject()->getAttributeObject()?->getUuid();
           }

           $creator = new DesignAttributeCreate(
               uuid: static::getClassUuid(),
               attribute_name: $this->getISystemAttribute()->getAttributeName(),
               owner_type_uuid: static::getClassOwningSystemType()::getDictionaryObject()->getTypeObject()->getUuid(),
               parent_attribute_uuid: $parent_uuid,
               is_final: $this->getISystemAttribute()::isFinal(),
               is_abstract: static::IS_ABSTRACT,
               access: TypeOfServerAccess::IS_PUBLIC_DOMAIN,

               value_policy: TypeOfElementValuePolicy::PER_CHILD,
               is_system: true,
               send_event: false
           );
           $creator->runAction();

           $what =  $creator->getAttribute();
           $this->b_did_create_model = true;
           return $what;
       } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }


    public function getAttributeObject() : ?Attribute {
        return $this->getAttribute();
    }



    public function getSystemHandle() : ?ISystemAttribute
    {
        return SystemAttributes::getAttributeByUuid(static::HANDLE_ATTRIBUTE_CLASS);
    }


    public function onCall(): ISystemResource
    {
        $this->getAttributeObject();
        return $this;
    }

    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        if (!$this->getISystemAttribute()::getSystemHandle()) {return;}
        $this->getAttributeObject()->design_attribute_id = static::getSystemHandle()->getAttributeObject()->id;
        $this->getAttributeObject()->save();
    }

    public function onNextStepB(): void {}
    public function onNextStepC(): void {}


    public static function isFinal(): bool
    {
        return static::IS_FINAL;
    }

    public static function isSystem(): bool
    {
        return true;
    }

    public static function isAbstract(): bool
    {
        return static::IS_ABSTRACT;
    }

    public static function isSeenChildrenTypes(): bool
    {
        return static::IS_SEEN_BY_CHILDREN_TYPES;
    }
    public function __construct(
        protected bool $b_type_init = false
    ) {

    }

 }
