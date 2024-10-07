<?php

namespace App\System\Collections;

use App\System\Resources\Servers\ISystemServer;
use App\System\SystemBase;

class SystemServers extends SystemBase
{
    const SOURCE_FOLDER = 'app/System/Servers/Stock';

    /**
     * @var ISystemServer[] $resource_array
     */
    protected static array $resource_array = [];

    public static function generateObjects() : array {
        /** @var ISystemServer[] $flat */
        $flat = parent::generateObjects();
        foreach ($flat as $flob) {
            static::$resource_array[$flob->getServerUuid()] = $flob;
        }
        return $flat;
    }

    public static function doNextStep() {
        foreach (static::$resource_array as $res) {
            $res->onNextStep();
        }
    }


    public static function getSystemServerByUuid(string $uuid) : ?ISystemServer {
        return static::$resource_array[$uuid]??null;
    }
}
