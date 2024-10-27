<?php

namespace App\Sys\Collections;

use App\Sys\Res\Types\ISystemType;

class SystemTypes extends SystemBase
{
    public static array $class_name_array;
    const SOURCE_FOLDER = 'app/Sys/Res/Types/Stk';

    public static function getTypeByUuid(string $class_name) : ?ISystemType {

        if (defined($class_name::UUID))  {
            /** @var ISystemType */
            return static::getResourceByUuid($class_name::UUID);
        }
        return null;
    }
}
