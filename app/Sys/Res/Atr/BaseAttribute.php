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


}
