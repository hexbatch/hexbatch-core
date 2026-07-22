<?php

namespace App\Sys\Res\Types\Stk\Root\Server;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Types\Stk\Root\About;
use App\Sys\Res\Types\Stk\Root\Server;


class ThisServerType extends Server
{

    public static function getClassUuid() : string {
        $name = config('hbc.system.server.uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace handle type uuid is not set in .env");
        }
        return $name;
    }

    public static function getTypeUuid(): string
    {
        return static::getClassUuid();
    }

    const TYPE_NAME = 'system_server';

    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [
        Server::class,
        About::class
    ];

}

