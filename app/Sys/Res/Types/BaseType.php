<?php

namespace App\Sys\Res\Types;


use App\Enums\Attributes\TypeOfServerAccess;
use App\Models\ElementType;
use App\Sys\Res\Atr\INewSystemAttribute;
use App\Sys\Res\DocumentTrait;
use App\Sys\Res\IDocument;


class BaseType implements IDocument, \JsonSerializable,INewSystemType
{
    use ActionableBaseTrait,DocumentTrait, GetFromArrayTrait,ChildrenTrait,GroupTrait;

    protected ?ElementType $type = null;

    const UUID = '';



    const bool IS_FINAL = false;


    const TYPE_NAME = '';
    const ATTRIBUTE_CLASSES = [];

    /** @type array<INewSystemType>  */
    const PARENT_CLASSES = [];

    const TypeOfServerAccess ACCESS_POLICY = TypeOfServerAccess::IS_PUBLIC_DOMAIN;


    public static function getTypeUuid(): string
    {
       return static::UUID;
    }

    public static function getTypeName(): string
    {
       return static::TYPE_NAME;
    }

    /**
     * @return array<string>
     */
    public static function getParentUuids(): array
    {
        $ret = [];
        foreach (static::PARENT_CLASSES as $c) {
            $ret[] = $c::getTypeUuid();
        }
        return $ret;
    }

    public static function getTypeAccessPolicy() : TypeOfServerAccess {
        return static::ACCESS_POLICY;
    }

    public static  function isTypeFinal(): bool { return static::IS_FINAL; }



    /**
     * @return INewSystemAttribute[]
     */
    public static function getAttributeClasses() :array {
        return static::ATTRIBUTE_CLASSES;
    }





    protected  function toArray() :array { throw new \LogicException("implement toArray");}

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }



}


