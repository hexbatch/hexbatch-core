<?php

namespace App\Sys\Collections;

use App\Sys\Res\Ele\ISystemElement;
use App\Sys\SystemBase;

class SystemElements extends SystemBase
{
    const SOURCE_FOLDER = 'app/Sys/Res/Ele/Stk';


    public static function getElementByUuid(string $uuid) : ?ISystemElement {
        /** @var ISystemElement */
        return static::getResourceByUuid($uuid);
    }
}
