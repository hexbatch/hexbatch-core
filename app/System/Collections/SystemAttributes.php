<?php

namespace App\System\Collections;

use App\System\Resources\Attributes\ISystemAttribute;
use App\System\SystemBase;

class SystemAttributes extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Attributes/Stock';


    public static function getAttributeByUuid(string $uuid) : ?ISystemAttribute {
        /** @var ISystemAttribute */
        return static::getResourceByUuid($uuid);
    }
}
