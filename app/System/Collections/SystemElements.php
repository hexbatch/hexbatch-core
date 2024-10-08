<?php

namespace App\System\Collections;

use App\System\Resources\Elements\ISystemElement;
use App\System\SystemBase;

class SystemElements extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Elements/Stock';


    public static function getSystemElementByUuid(string $uuid) : ?ISystemElement {
        /** @var ISystemElement */
        return static::getResourceByUuid($uuid);
    }
}
