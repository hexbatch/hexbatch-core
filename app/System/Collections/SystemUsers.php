<?php

namespace App\System\Collections;

use App\System\Resources\Users\ISystemUser;
use App\System\SystemBase;

class SystemUsers extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Users/Stock';


    public static function getSystemUserByUuid(string $uuid) : ?ISystemUser {
        /** @var ISystemUser */
        return static::getResourceByUuid($uuid);
    }
}
