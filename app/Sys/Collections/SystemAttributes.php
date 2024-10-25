<?php

namespace App\Sys\Collections;

use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\SystemBase;

class SystemAttributes extends SystemBase
{
    public static array $class_name_array;
    const SOURCE_FOLDER = 'app/Sys/Res/Atr/Stk';


    public static function getAttributeByUuid(string $class_name) : ?ISystemAttribute {
        if (defined($class_name::UUID))  {
            /** @var ISystemAttribute */
            return static::getResourceByUuid($class_name::UUID);
        }
        return null;
    }
}
