<?php

namespace App\Sys\Collections;

use App\Models\ElementType;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\ISystemType;

class SystemTypes extends SystemBase
{
    public static array $class_name_array;
    protected static array $uuid_class_names = [];
    const SOURCE_FOLDER = 'app/Sys/Res/Types/Stk';

    /** @var array<string,ISystemResource> $resource_array */
    protected static array $resource_array = []; //keyed by uuid

    /** @var array<string,ISystemType> $attribute_to_type_uuid */
    protected static array $attribute_to_type_uuid = [];

    public static function getTypeByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemType {

        /** @var ISystemType */
        return static::getResourceByUuid($class_name_or_uuid);
    }

    protected static function makeNewClass(string $some_class_name) : ISystemResource {
        /** @type BaseType*/
        return new $some_class_name(b_type_init:true);
    }



    protected  static function postFindClasses() : void {
        static::$attribute_to_type_uuid = [];

        /**
         * @var BaseType $sys_type_class
         */
        foreach (static::$class_name_array as $sys_type_class) {
            foreach ($sys_type_class::getAttributeClasses() as $att) {
                static::$attribute_to_type_uuid[$att::getClassUuid()] = $sys_type_class;
            }
        }
    }

    public static function getAttributeOwner(null|string|ISystemResource  $class_name_or_uuid)  : string|null|ISystemType
    {
        $attribute_uuid = static::getUuid($class_name_or_uuid);
        if (!$attribute_uuid) {return null;}
        return static::$attribute_to_type_uuid[$attribute_uuid]??null;
    }

    /**
     * returns array of models that are current between resources and db
     * * @return ISystemModel[]
     */
    public static function getCurrentModels() :array {
        $uuids = static::getUuids();
        $prepped_uuids = array_map(fn($value): string => "?", $uuids);
        $uuids_comma_delimited = implode(',',$prepped_uuids);
        $models = ElementType::whereRaw("ref_uuid  in ($uuids_comma_delimited)",$uuids)
            /** @uses ElementType::owner_namespace(),\App\Models\UserNamespace::namespace_home_server() */
            ->with('owner_namespace','owner_namespace.namespace_home_server')
            ->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;
    }

    /**
     * returns array of models that no longer fit with the resources
     * @return ISystemModel[]
     */
    public static function getOldModels() :array  {
        $uuids = static::getUuids();
        $prepped_uuids = array_map(fn($value): string => "?", $uuids);
        $uuids_comma_delimited = implode(',',$prepped_uuids);

        $models = ElementType::where('is_system',true)
            ->whereRaw("ref_uuid not in ($uuids_comma_delimited)",$uuids)
            /** @uses ElementType::owner_namespace(),\App\Models\UserNamespace::namespace_home_server() */
            ->with('owner_namespace','owner_namespace.namespace_home_server')
            ->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;

    }

    public static function doNextStep() {
        parent::doNextStep();
    }
    public static function doNextStepB() {
        foreach (static::$resource_array as $res) {
            $res->onNextStepB();
        }
    }

    public static function doNextStepC() {
        foreach (static::$resource_array as $res) {
            $res->onNextStepC();
        }
    }

}
