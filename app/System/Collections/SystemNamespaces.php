<?php

namespace App\System\Collections;

use App\System\Resources\Namespaces\ISystemNamespace;
use App\System\SystemBase;

class SystemNamespaces extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Namespaces/Stock';


    public static function getNamespaceByUuid(string $uuid) : ?ISystemNamespace {
        /** @var ISystemNamespace */
        return static::getResourceByUuid($uuid);
    }
}
