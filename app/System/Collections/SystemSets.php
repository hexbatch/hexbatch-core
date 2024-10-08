<?php

namespace App\System\Collections;

use App\System\Resources\Sets\ISystemSet;
use App\System\SystemBase;

class SystemSets extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Sets/Stock';


    public static function getSetByUuid(string $uuid) : ?ISystemSet {
        /** @var ISystemSet */
        return static::getResourceByUuid($uuid);
    }
}
