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

    public static function generateObjects() : void
    {
        SystemUsers::generateObjects();
        SystemNamespaces::generateObjects();
        SystemTypes::generateObjects();
        //do not generate the attributes here, the types will call them
        SystemElements::generateObjects();
        SystemServers::generateObjects();
        SystemSets::generateObjects();


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

