<?php

namespace App\Sys\Res\Types;


use App\Enums\Attributes\TypeOfServerAccess;
use App\Models\ElementType;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\DocumentTrait;
use App\Sys\Res\IDocument;


class BaseType implements IDocument, \JsonSerializable,INewSystemType
{
    use ActionableBaseTrait,DocumentTrait, GetFromArrayTrait,ChildrenTrait,GroupTrait;

    protected ?ElementType $type = null;

    const UUID = '';



    const IS_FINAL = false;


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

    public static  function isFinal(): bool { return static::IS_FINAL; }




    public static function getClassUuid() : string {
        return static::UUID;
    }



    /**
     * @return ISystemAttribute[]
     */
    public static function getAttributeClasses() :array {
        return static::ATTRIBUTE_CLASSES;
    }





    public static function getParentNameTree() :array  {
        $ret = [];
        $ret[static::getHexbatchClassName()] = [] ;
        foreach (static::PARENT_CLASSES as $full_class_name) {
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret[static::getHexbatchClassName()][] = $full_class_name::getParentNameTree();
            }
        }
        return $ret;
    }

    public static function getFlatInheritance() : string  {
        $raw = static::renderSubtree(static::getParentNameTree());
        return preg_replace('/(\|~\|\d)/', "\n   ",$raw);
    }

    public static function renderSubtree(array $tree) : string  {
        $ret = [];

        $count = 0;
        foreach ($tree as $k => $v) {
            if ($count) {
                $ret[] = '~';
            }
            if ($k) {
                $ret[] = $k;
            }

            if (count($v) ) {
                $what = static::renderSubtree($v);
                $ret[] = $what;

            }
            $count++;

        }


        return implode('|',$ret);
    }



    public static function getHexbatchClassName() :string { return static::TYPE_NAME; }







    protected  function toArray() :array { throw new \LogicException("implement toArray");}

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }



}


