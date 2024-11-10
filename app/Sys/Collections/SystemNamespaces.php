<?php

namespace App\Sys\Collections;

use App\Models\UserNamespace;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;

class SystemNamespaces extends SystemBase
{
    public static array $class_name_array;
    protected static array $uuid_class_names = [];
    const SOURCE_FOLDER = 'app/Sys/Res/Namespaces/Stock';


    public static function getNamespaceByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemNamespace {

        /** @var ISystemNamespace */
        return static::getResourceByUuid($class_name_or_uuid);
    }

    /**
     * returns array of models that are current between resources and db
     * * @return UserNamespace[]
     */
    public static function getCurrentModels() :array {
        $uuids = static::getUuids();
        $prepped_uuids = array_map(fn($value): string => "?", $uuids);
        $uuids_comma_delimited = implode(',',$prepped_uuids);
        $models = UserNamespace::whereRaw("ref_uuid  in ($uuids_comma_delimited)",$uuids)->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;
    }

    /**
     * returns array of models that no longer fit with the resources
     * @return UserNamespace[]
     */
    public static function getOldModels() :array  {
        $models = UserNamespace::where('is_system',true)->get();
        $ret = [];
        foreach ($models as $mod) {
            $ret[] = $mod;
        }
        return $ret;

    }

}
