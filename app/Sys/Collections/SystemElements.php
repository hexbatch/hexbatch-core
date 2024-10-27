<?php

namespace App\Sys\Collections;

use App\Sys\Res\Ele\ISystemElement;

class SystemElements extends SystemBase
{
    public static array $class_name_array;
    const SOURCE_FOLDER = 'app/Sys/Res/Ele/Stk';


    public static function getElementByUuid(string $class_name) : ?ISystemElement {
        if (defined($class_name::UUID))  {
            /** @var ISystemElement */
            return static::getResourceByUuid($class_name::UUID);
        }
        return null;
    }
}
