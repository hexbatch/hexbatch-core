<?php

namespace App\Sys\Collections;

use App\Sys\Res\Sets\ISystemSet;
use App\Sys\SystemBase;

class SystemSets extends SystemBase
{
    public static array $class_name_array;
    const SOURCE_FOLDER = 'app/Sys/Res/Sets/Stock';


    public static function getSetByUuid(string $class_name) : ?ISystemSet {

        if (defined($class_name::UUID))  {
            /** @var ISystemSet */
            return static::getResourceByUuid($class_name::UUID);
        }
        return null;
    }
}
