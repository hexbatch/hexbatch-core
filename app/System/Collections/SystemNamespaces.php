<?php

namespace App\System\Collections;

use App\System\Resources\Namespaces\ISystemNamespace;
use App\System\SystemBase;

class SystemNamespaces extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Namespaces/Stock';

    /**
     * @var ISystemNamespace[] $resource_array
     */
    protected static array $resource_array = [];

    public static function generateObjects() : array {
        /** @var ISystemNamespace[] $flat */
        $flat = parent::generateObjects();
        foreach ($flat as $flob) {
            static::$resource_array[$flob->getNamespaceUuid()] = $flob;
        }
        return $flat;
    }

    public static function doNextStep() {
        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }


    public static function getSystemNamespaceByUuid(string $uuid) : ?ISystemNamespace {
        return static::$resource_array[$uuid]??null;
    }
}
