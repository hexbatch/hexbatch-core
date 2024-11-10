<?php

namespace App\Sys\Collections;

use App\Models\User;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Users\ISystemUser;

class SystemUsers extends SystemBase
{
    /** @var  ISystemResource[] $class_name_array */
    public static array $class_name_array;

    /** @var array<string,SystemBase> $uuid_class_names */
    protected static array $uuid_class_names = [];
    const SOURCE_FOLDER = 'app/Sys/Res/Users/Stock';

    /** @var array<string,ISystemResource> $resource_array */
    protected static array $resource_array = []; //keyed by uuid


    public static function getSystemUserByUuid(null|string|ISystemResource  $class_name_or_uuid) : ?ISystemUser {

        /** @var ISystemUser */
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
        $users = User::whereRaw("ref_uuid  in ($uuids_comma_delimited)",$uuids)->get();
        $ret = [];
        foreach ($users as $user) {
            $ret[] = $user;
        }
        return $ret;
    }


    /**
     * returns array of models that no longer fit with the resources
     * @return ISystemModel[]
     */
    public static function getOldModels() :array {
        $uuids = static::getUuids();
        $prepped_uuids = array_map(fn($value): string => "?", $uuids);
        $uuids_comma_delimited = implode(',',$prepped_uuids);

        $users = User::where('is_system',true)
            ->whereRaw("ref_uuid not in ($uuids_comma_delimited)",$uuids)
            ->get();
        $ret = [];
        foreach ($users as $user) {
            $ret[] = $user;
        }
        return $ret;


    }




}
