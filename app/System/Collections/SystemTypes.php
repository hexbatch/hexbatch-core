<?php

namespace App\System\Collections;

use App\System\Resources\Types\ISystemType;
use App\System\SystemBase;

class SystemTypes extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Types/Stock';

    /**
     * @var ISystemType[] $resource_array
     */
    protected static array $resource_array = [];

    public static function generateObjects() : array {
        /** @var ISystemType[] $flat */
        $flat = parent::generateObjects();
        foreach ($flat as $flob) {
            static::$resource_array[$flob->getTypeUuid()] = $flob;
        }
        return $flat;
    }

    public static function doNextStep() {
        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }


    public static function getSystemTypeByUuid(string $uuid) : ?ISystemType {
        return static::$resource_array[$uuid]??null;
    }
}
