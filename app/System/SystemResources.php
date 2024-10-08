<?php

namespace App\System;

use App\System\Collections\SystemAttributes;
use App\System\Collections\SystemElements;
use App\System\Collections\SystemNamespaces;
use App\System\Collections\SystemServers;
use App\System\Collections\SystemSets;
use App\System\Collections\SystemTypes;
use App\System\Collections\SystemUsers;

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

