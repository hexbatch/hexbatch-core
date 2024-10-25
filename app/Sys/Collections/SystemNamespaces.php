<?php

namespace App\Sys\Collections;

use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\SystemBase;

class SystemNamespaces extends SystemBase
{
    public static array $class_name_array;
    const SOURCE_FOLDER = 'app/Sys/Res/Namespaces/Stock';


    public static function getNamespaceByUuid(string $class_name) : ?ISystemNamespace {

        if (defined($class_name::UUID))  {
            /** @var ISystemNamespace */
            return static::getResourceByUuid($class_name::UUID);
        }
        return null;
    }
}
