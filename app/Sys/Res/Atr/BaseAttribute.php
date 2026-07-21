<?php

namespace App\Sys\Res\Atr;


use App\Data\ApiParams\Rules\ValidateNamespaceRef;
use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Models\Attribute;
use App\Sys\Res\DocumentTrait;
use App\Sys\Res\IDocument;


class BaseAttribute implements IDocument,INewSystemAttribute
{
    use DocumentTrait;

    protected ?Attribute $attribute = null;

    const UUID = '';
    /** @type INewSystemAttribute  */
    const PARENT_ATTRIBUTE_CLASS = '';
    const ATTRIBUTE_NAME = '';

    const IS_FINAL = false;
    const IS_ABSTRACT = false;

    const LOCATION_UUID = null;
    const DESIGN_ATTRIBUTE_UUID = null;
    const DEFAULT_VALUE = null;
    const ?string JSON_READ_PATH = null;
    const ?string JSON_WRITE_PATH = null;

    const TypeOfElementValuePolicy VALUE_POLICY = TypeOfElementValuePolicy::PER_SET;
    const TypeOfServerAccess ACCESS_POLICY = TypeOfServerAccess::IS_PUBLIC_DOMAIN;


    public static function getAttributeName(): string
    {
        return static::ATTRIBUTE_NAME;
    }

    public static function getAttributeUuid(): string
    {
        return static::UUID;
    }

    public static function getAttributeParentUuid(): ?string
    {
        if (!static::PARENT_ATTRIBUTE_CLASS) {return null;}
        return static::PARENT_ATTRIBUTE_CLASS::getAttributeUuid();
    }

    public static function getAttributeLocationUuid(): ?string
    {
        return static::LOCATION_UUID;
    }

    public static function getAttributeDesignUuid(): ?string
    {
        return static::DESIGN_ATTRIBUTE_UUID;
    }

    public static function isAttributeFinal(): bool
    {
        return static::IS_FINAL;
    }

    public static function isAttributeAbstract(): bool
    {
        return static::IS_ABSTRACT;
    }

    public static function getValuePolicy(): TypeOfElementValuePolicy
    {
        return static::VALUE_POLICY;
    }

    public static function getAccessPolicy(): TypeOfServerAccess
    {
       return static::ACCESS_POLICY;
    }

    public static function getReadJsonPath(): ?string
    {
        return static::JSON_READ_PATH;
    }

    public static function getValidateJsonPath(): ?string
    {
        return static::JSON_WRITE_PATH;
    }

    public static function getAttributeDefaultValue(): ?array
    {
        return static::DEFAULT_VALUE;
    }




     public static function getClassUuid() : string {
         return static::UUID;
     }




     public static function getHexbatchClassName() :string { return static::ATTRIBUTE_NAME; }


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
          * @var INewSystemAttribute[] $rev
          */
        $rev = array_reverse(static::getParentClasses());

        foreach ($rev as $parent_class) {
            $names[] = $parent_class::getHexbatchClassName();
        }
        $names[] = static::getAttributeName();
        return implode(ValidateNamespaceRef::NAMESPACE_SEPERATOR,$names);
     }


    public static function isSystem(): bool
    {
        return true;
    }



}
