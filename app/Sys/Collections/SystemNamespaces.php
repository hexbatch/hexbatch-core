<?php

namespace App\Sys\Collections;

use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\SystemBase;

class SystemNamespaces extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Namespaces/Stock';


    public static function getNamespaceByUuid(string $uuid) : ?ISystemNamespace {
        /** @var ISystemNamespace */
        return static::getResourceByUuid($uuid);
    }
}
