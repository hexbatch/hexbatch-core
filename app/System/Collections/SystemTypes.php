<?php

namespace App\System\Collections;

use App\System\Resources\Types\ISystemType;
use App\System\SystemBase;

class SystemTypes extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Types/Stock';

    public static function getTypeByUuid(string $uuid) : ?ISystemType {
        /** @var ISystemType */
        return static::getResourceByUuid($uuid);
    }
}
