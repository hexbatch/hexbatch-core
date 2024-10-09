<?php

namespace App\Sys\Collections;

use App\Sys\Res\Elements\ISystemElement;
use App\Sys\SystemBase;

class SystemElements extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Elements/Stock';


    public static function getElementByUuid(string $uuid) : ?ISystemElement {
        /** @var ISystemElement */
        return static::getResourceByUuid($uuid);
    }
}
