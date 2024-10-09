<?php

namespace App\Sys\Collections;

use App\Sys\Res\Types\ISystemType;
use App\Sys\SystemBase;

class SystemTypes extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Types/Stock';

    public static function getTypeByUuid(string $uuid) : ?ISystemType {
        /** @var ISystemType */
        return static::getResourceByUuid($uuid);
    }
}
