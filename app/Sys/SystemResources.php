<?php

namespace App\Sys;

use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Collections\SystemSets;
use App\Sys\Collections\SystemTypes;
use App\Sys\Collections\SystemUsers;

class SystemResources
{

    const MAX_SYSTEM_RESOURCE_NAME_LENGTH = 16;
    const MAX_SYSTEM_RESOURCE_NESTING = 16;

    public static function getUuidDictionary() : array {
        return SystemBase::getUuidClassNames();
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

    public static function generateObjects() : array
    {
        $ret = [];
        $ret =array_merge(SystemUsers::generateObjects(),$ret);
        $ret =array_merge(SystemNamespaces::generateObjects(),$ret);
        $ret =array_merge(SystemTypes::generateObjects(),$ret);
        //do not generate the attributes here, the types will call them
        $ret =array_merge(SystemAttributes::generateObjects(),$ret);
        $ret =array_merge(SystemElements::generateObjects(),$ret);
        $ret =array_merge(SystemServers::generateObjects(),$ret);
        $ret =array_merge(SystemSets::generateObjects(),$ret);

        return $ret;

    }

    public static function doNextSteps() : void
    {


        //then call the second step to initialize the rest
        SystemUsers::doNextStep();
        SystemNamespaces::doNextStep();
        SystemTypes::doNextStep();
        SystemAttributes::doNextStep(); //do after the types

        SystemElements::doNextStep();
        SystemSets::doNextStep();

        SystemServers::doNextStep(); //do last



    }
}

