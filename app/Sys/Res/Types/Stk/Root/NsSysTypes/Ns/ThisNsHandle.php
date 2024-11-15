<?php

namespace App\Sys\Res\Types\Stk\Root\NsSysTypes\Ns;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Handle;
use App\Sys\Res\Types\Stk\Root\Handles\TypeHandle;
use App\Sys\Res\Types\Stk\Root\NsSysTypes\ThisNsType;


class ThisNsHandle extends TypeHandle
{

    public static function getClassUuid() : string {
        $name = config('hbc.system.server.type_uuid');
        if (!$name) {
            throw new HexbatchInitException("System server type uuid is not set in .env");
        }
        return $name;
    }

    const TYPE_NAME = 'system_namespace_handle';

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;


    const PARENT_CLASSES = [
        ThisNsType::class,
        TypeHandle::class
    ];

}

