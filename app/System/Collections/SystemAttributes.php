<?php

namespace App\System\Collections;

use App\System\Resources\Attributes\ISystemAttribute;
use App\System\SystemBase;

class SystemAttributes extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Attributes/Stock';

    /**
     * @var ISystemAttribute[] $resource_array
     */
    protected static array $resource_array = [];

    public static function generateObjects() : array {
        /** @var ISystemAttribute[] $flat */
        $flat = parent::generateObjects();
        foreach ($flat as $flob) {
            static::$resource_array[$flob->getAttributeUuid()] = $flob;
        }
        return $flat;
    }

    public static function doNextStep() {
        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }


    public static function getSystemAttributeByUuid(string $uuid) : ?ISystemAttribute {
        return static::$resource_array[$uuid]??null;
    }
}
