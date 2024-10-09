<?php

namespace App\Sys\Collections;

use App\Sys\Res\Users\ISystemUser;
use App\Sys\SystemBase;

class SystemUsers extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Users/Stock';


    public static function getSystemUserByUuid(string $uuid) : ?ISystemUser {
        /** @var ISystemUser */
        return static::getResourceByUuid($uuid);
    }
}
