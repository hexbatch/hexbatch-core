<?php

namespace App\System\Collections;

use App\System\Resources\Users\ISystemUser;
use App\System\SystemBase;

class SystemUsers extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Users/Stock';

    protected static array $resource_array = [];

    public static function generateObjects() : array {
        /** @var ISystemUser[] $flat */
        $flat = parent::generateObjects();
        foreach ($flat as $flob) {
            static::$resource_array[$flob->getUserUuid()] = $flob;
        }
        return $flat;
    }


    public function getSystemUserByUuid(string $uuid) : ?ISystemUser {
        return static::$resource_array[$uuid]??null;
    }
}
