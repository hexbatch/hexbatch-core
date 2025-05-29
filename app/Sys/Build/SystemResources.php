<?php

namespace App\Sys\Build;

use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemBase;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Collections\SystemSets;
use App\Sys\Collections\SystemTypes;
use App\Sys\Collections\SystemUsers;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\ISystemResource;
use Illuminate\Support\Facades\DB;

class SystemResources
{


    /** @var array<string,SystemBase> $uuid_class_dictionary */
    protected static array $uuid_class_dictionary = [];

    public static function addToUuidDictionary(string $uuid, $sys) {
        static::$uuid_class_dictionary[$uuid] = $sys;
    }

    public static function getUuidDictionary() : array {
        return static::$uuid_class_dictionary;
    }
    public static function loadClasses() : array
    {
        $ret = [];
        $ret =array_merge(SystemUsers::loadClasses(),$ret);
        $ret =array_merge(SystemNamespaces::loadClasses(),$ret);
        $ret =array_merge(SystemTypes::loadClasses(),$ret);
        $ret =array_merge(SystemAttributes::loadClasses(),$ret);
        $ret =array_merge(SystemElements::loadClasses(),$ret);
        $ret =array_merge(SystemServers::loadClasses(),$ret);
        $ret =array_merge(SystemSets::loadClasses(),$ret);

        return $ret;

    }

    /**
     * @return array<string,ISystemResource[]>
     */
    public static function generateObjects() : array
    {
        $ret = [];
        $ret['users'] =SystemUsers::generateObjects();
        $ret['namespaces'] =SystemNamespaces::generateObjects();
        $ret['types'] =SystemTypes::generateObjects();
        SystemTypes::doNextStep(); //type parents

        $ret['attributes'] =SystemAttributes::generateObjects();

        SystemTypes::doNextStepB(); //publish

        $ret['elements'] =SystemElements::generateObjects();
        $ret['sets'] =SystemSets::generateObjects();
        $ret['servers'] =SystemServers::generateObjects();
        return $ret;

    }

    public static function doNextSteps() : void
    {


        //then call the second step to initialize the rest, if model not created in last step then nothing done in that class
        SystemUsers::doNextStep();
        SystemNamespaces::doNextStep();

        SystemAttributes::doNextStep(); //do after the types

        SystemElements::doNextStep();
        SystemSets::doNextStep();

        SystemServers::doNextStep();

        SystemTypes::doNextStepC(); //setup element handlers

    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function build() {
        try {
            DB::beginTransaction();
            $ret = SystemResources::generateObjects();
            SystemResources::doNextSteps();
            DB::commit();
            return $ret;
        } catch (\Exception|\Error $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return array<string,ISystemModel[]>
     */
    public static function showOld() : array
    {
        $ret = [];
        $ret['users'] =SystemUsers::getOldModels();
        $ret['namespaces'] =SystemNamespaces::getOldModels();
        $ret['types'] =SystemTypes::getOldModels();
        $ret['attributes'] =SystemAttributes::getOldModels();
        $ret['elements'] =SystemElements::getOldModels();
        $ret['sets'] =SystemSets::getOldModels();
        $ret['servers'] =SystemServers::getOldModels();
        return $ret;

    }

    /**
     * @return array<string,ISystemResource[]>
     */
    public static function showNew() : array
    {
        static::loadClasses();
        $ret = [];
        $ret['users'] =SystemUsers::getNew();
        $ret['namespaces'] =SystemNamespaces::getNew();
        $ret['types'] =SystemTypes::getNew();
        $ret['attributes'] =SystemAttributes::getNew();
        $ret['elements'] =SystemElements::getNew();
        $ret['sets'] =SystemSets::getNew();
        $ret['servers'] =SystemServers::getNew();
        return $ret;

    }

    public static function removeOld() : array
    {
        $ret = [];
        $ret['users'] =SystemUsers::removeOld();
        $ret['namespaces'] =SystemNamespaces::removeOld();
        $ret['types'] =SystemTypes::removeOld();
        $ret['attributes'] =SystemAttributes::removeOld();
        $ret['elements'] =SystemElements::removeOld();
        $ret['sets'] =SystemSets::removeOld();
        $ret['servers'] =SystemServers::removeOld();
        return $ret;

    }

    /**
     * @return array<string,ISystemModel[]>
     */
    public static function showCurrent() : array
    {
        $ret = [];
        $ret['users'] =SystemUsers::getCurrentModels();
        $ret['namespaces'] =SystemNamespaces::getCurrentModels();
        $ret['types'] =SystemTypes::getCurrentModels();
        $ret['attributes'] =SystemAttributes::getCurrentModels();
        $ret['elements'] =SystemElements::getCurrentModels();
        $ret['sets'] =SystemSets::getCurrentModels();
        $ret['servers'] =SystemServers::getCurrentModels();
        return $ret;

    }
}

