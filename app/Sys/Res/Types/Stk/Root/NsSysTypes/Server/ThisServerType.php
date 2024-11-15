<?php

namespace App\Sys\Res\Types\Stk\Root\NsSysTypes\Server;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\Stk\Root\About;
use App\Sys\Res\Types\Stk\Root\NsSysTypes\ThisNsType;
use App\Sys\Res\Types\Stk\Root\Server;


class ThisServerType extends Server
{

    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.types.handle_type_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace handle type uuid is not set in .env");
        }
        return $name;
    }

    const TYPE_NAME = 'system_server';

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;


    const PARENT_CLASSES = [
        ThisNsType::class,
        Server::class,
        About::class
    ];

}

