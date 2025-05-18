<?php

namespace App\Sys\Collections;

use App\Models\Element;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\ISystemResource;

class SystemElements extends SystemBase
{
    public static array $class_name_array;
    protected static array $uuid_class_names = [];
    const string SOURCE_FOLDER = 'app/Sys/Res/Ele/Stk';

    /** @var array<string,ISystemResource> $resource_array */
    protected static array $resource_array = []; //keyed by uuid

    public static function getElementByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemElement {
        /** @var ISystemElement */
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
        $models = Element::whereRaw("ref_uuid  in ($uuids_comma_delimited)",$uuids)
            /** @uses Element::element_parent_type() */
            ->with('element_parent_type')
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

        $models = Element::where('is_system',true)
            ->whereRaw("ref_uuid not in ($uuids_comma_delimited)",$uuids)
            /** @uses Element::element_parent_type() */
            ->with('element_parent_type')
            ->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;

    }

}
