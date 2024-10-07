<?php

namespace App\System\Collections;

use App\System\Resources\Sets\ISystemSet;
use App\System\SystemBase;

class SystemSets extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Sets/Stock';

    /**
     * @var ISystemSet[] $resource_array
     */
    protected static array $resource_array = [];

    public static function generateObjects() : array {
        /** @var ISystemSet[] $flat */
        $flat = parent::generateObjects();
        foreach ($flat as $flob) {
            static::$resource_array[$flob->getSetUuid()] = $flob;
        }
        return $flat;
    }

    public static function doNextStep() {
        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }


    public static function getSystemTypeByUuid(string $uuid) : ?ISystemSet {
        return static::$resource_array[$uuid]??null;
    }
}
