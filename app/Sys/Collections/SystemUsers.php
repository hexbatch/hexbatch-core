<?php

namespace App\Sys\Collections;

use App\Sys\Res\Users\ISystemUser;
use App\Sys\SystemBase;

class SystemUsers extends SystemBase
{
    public static array $class_name_array;
    const SOURCE_FOLDER = 'app/Sys/Res/Users/Stock';


    public static function getSystemUserByUuid(string $class_name) : ?ISystemUser {

        if (defined($class_name::UUID))  {
            /** @var ISystemUser */
            return static::getResourceByUuid($class_name::UUID);
        }
        return null;
    }
}
