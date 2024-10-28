<?php

namespace App\Sys\Res\Types\Stk\Root\NS\ThisServer\ThisServerNs;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Handle;
use App\Sys\Res\Types\Stk\Root\NS\ThisServer\ThisServerNS;


class ThisServerHandle extends BaseType
{

    public static function getUuid() : string {
        $name = config('hbc.system.namespace.types.handle_type_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace handle type uuid is not set in .env");
        }
        return $name;
    }

    const TYPE_NAME = 'system_handle';

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;


    const PARENT_CLASSES = [
        ThisServerNS::class,
        Handle::class
    ];

}

