<?php

namespace App\Sys\Collections;

use App\Sys\Res\Attributes\ISystemAttribute;
use App\Sys\SystemBase;

class SystemAttributes extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Attributes/Stock';


    public static function getAttributeByUuid(string $uuid) : ?ISystemAttribute {
        /** @var ISystemAttribute */
        return static::getResourceByUuid($uuid);
    }
}
