<?php

namespace App\Sys\Collections;

use App\Sys\Res\Sets\ISystemSet;
use App\Sys\SystemBase;

class SystemSets extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Sets/Stock';


    public static function getSetByUuid(string $uuid) : ?ISystemSet {
        /** @var ISystemSet */
        return static::getResourceByUuid($uuid);
    }
}
