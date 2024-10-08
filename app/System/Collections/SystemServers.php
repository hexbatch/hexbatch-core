<?php

namespace App\System\Collections;

use App\System\Resources\Servers\ISystemServer;
use App\System\SystemBase;

class SystemServers extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Servers/Stock';


    public static function getServerByUuid(string $uuid) : ?ISystemServer {
        /** @var ISystemServer */
        return static::getResourceByUuid($uuid);
    }
}
