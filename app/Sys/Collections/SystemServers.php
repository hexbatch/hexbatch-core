<?php

namespace App\Sys\Collections;

use App\Sys\Res\Servers\ISystemServer;
use App\Sys\SystemBase;

class SystemServers extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Servers/Stock';


    public static function getServerByUuid(string $uuid) : ?ISystemServer {
        /** @var ISystemServer */
        return static::getResourceByUuid($uuid);
    }
}
