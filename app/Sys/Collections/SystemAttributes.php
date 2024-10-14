<?php

namespace App\Sys\Collections;

use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\SystemBase;

class SystemAttributes extends SystemBase
{
    const SOURCE_FOLDER = 'app/Sys/Res/Atr/Stk';


    public static function getAttributeByUuid(string $uuid) : ?ISystemAttribute {
        /** @var ISystemAttribute */
        return static::getResourceByUuid($uuid);
    }
}
