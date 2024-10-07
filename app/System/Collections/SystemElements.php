<?php

namespace App\System\Collections;

use App\System\Resources\Elements\ISystemElement;
use App\System\SystemBase;

class SystemElements extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Elements/Stock';

    /**
     * @var ISystemElement[] $resource_array
     */
    protected static array $resource_array = [];

    public static function generateObjects() : array {
        /** @var ISystemElement[] $flat */
        $flat = parent::generateObjects();
        foreach ($flat as $flob) {
            static::$resource_array[$flob->getElementUuid()] = $flob;
        }
        return $flat;
    }

    public static function doNextStep() {
        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }


    public static function getSystemElementByUuid(string $uuid) : ?ISystemElement {
        return static::$resource_array[$uuid]??null;
    }
}
