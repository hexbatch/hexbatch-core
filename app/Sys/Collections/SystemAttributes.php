<?php

namespace App\Sys\Collections;

use App\Models\Attribute;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\ISystemResource;

class SystemAttributes extends SystemBase
{
    public static array $class_name_array;
    protected static array $uuid_class_names = [];

    /** @var array<string,ISystemResource> $resource_array */
    protected static array $resource_array = []; //keyed by uuid

    const SOURCE_FOLDER = 'app/Sys/Res/Atr/Stk';


    public static function getAttributeByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemAttribute {
        /** @var ISystemAttribute */
        return static::getResourceByUuid($class_name_or_uuid);
    }

    /**
    * returns array of models that are current between resources and db
    * * @return ISystemModel[]
    */
    public static function getCurrentModels() :array {
        $uuids = static::getUuids();
        $prepped_uuids = array_map(fn($value): string => "?", $uuids);
        $uuids_comma_delimited = implode(',',$prepped_uuids);
        $models = Attribute::whereRaw("ref_uuid  in ($uuids_comma_delimited)",$uuids)
            /** @uses Attribute::type_owner() */
            ->with('type_owner')
            /** @uses ElementType::owner_namespace(),\App\Models\UserNamespace::namespace_home_server() */
            ->with('type_owner.owner_namespace','type_owner.owner_namespace.namespace_home_server')
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

        $models = Attribute::where('is_system',true)
            ->whereRaw("ref_uuid not in ($uuids_comma_delimited)",$uuids)
            /** @uses Attribute::type_owner() */
            ->with('type_owner')
            /** @uses ElementType::owner_namespace(),\App\Models\UserNamespace::namespace_home_server() */
            ->with('type_owner.owner_namespace','type_owner.owner_namespace.namespace_home_server')
            ->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;

    }

}
