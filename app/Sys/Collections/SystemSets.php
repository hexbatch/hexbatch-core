<?php

namespace App\Sys\Collections;

use App\Models\ElementSet;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Sets\ISystemSet;

class SystemSets extends SystemBase
{
    public static array $class_name_array;
    protected static array $uuid_class_names = [];
    const SOURCE_FOLDER = 'app/Sys/Res/Sets/Stock';

    /** @var array<string,ISystemResource> $resource_array */
    protected static array $resource_array = []; //keyed by uuid

    public static function getSetByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemSet {
        /** @var ISystemSet */
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
        $models = ElementSet::whereRaw("ref_uuid  in ($uuids_comma_delimited)",$uuids)
            /** @uses ElementSet::defining_element() */
            ->with('defining_element')
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

        $models = ElementSet::where('is_system',true)
            ->whereRaw("ref_uuid not in ($uuids_comma_delimited)",$uuids)
            /** @uses ElementSet::defining_element() */
            ->with('defining_element')
            ->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;

    }

}
