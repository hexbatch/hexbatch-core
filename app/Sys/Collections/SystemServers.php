<?php

namespace App\Sys\Collections;

use App\Sys\Res\Servers\ISystemServer;

class SystemServers extends SystemBase
{
    public static array $class_name_array;
    const SOURCE_FOLDER = 'app/Sys/Res/Servers/Stock';


    public static function getServerByUuid(string $class_name) : ?ISystemServer {
        if (defined($class_name::UUID))  {
            /** @var ISystemServer */
            return static::getResourceByUuid($class_name::UUID);
        }
        return null;
    }
}
