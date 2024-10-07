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
        SystemAttributes::generateObjects();
        SystemElements::generateObjects();
        SystemServers::generateObjects();
        SystemSets::generateObjects();


        //then call the second step to initialize the rest
        SystemUsers::doNextStep();
        SystemNamespaces::doNextStep();
        SystemTypes::doNextStep();
        SystemAttributes::doNextStep();
        SystemServers::doNextStep();
        SystemElements::doNextStep();
        SystemSets::doNextStep();

    }
}

