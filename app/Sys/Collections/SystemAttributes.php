<?php

namespace App\Sys\Collections;

use App\Models\Attribute;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\ISystemResource;

class SystemAttributes extends SystemBase
{
    public static array $class_name_array;
    protected static array $uuid_class_names = [];
    const SOURCE_FOLDER = 'app/Sys/Res/Atr/Stk';


    public static function getAttributeByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemAttribute {
        /** @var ISystemAttribute */
        return static::getResourceByUuid($class_name_or_uuid);
    }

    /**
    * returns array of models that are current between resources and db
    * * @return Attribute[]
    */
    public static function getCurrentModels() :array {
        $uuids = static::getUuids();
        $prepped_uuids = array_map(fn($value): string => "?", $uuids);
        $uuids_comma_delimited = implode(',',$prepped_uuids);
        $models = Attribute::whereRaw("ref_uuid  in ($uuids_comma_delimited)",$uuids)->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;
    }

    /**
     * returns array of models that no longer fit with the resources
     * @return Attribute[]
     */
    public static function getOldModels() :array  {
        $models = Attribute::where('is_system',true)->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;

    }

}
