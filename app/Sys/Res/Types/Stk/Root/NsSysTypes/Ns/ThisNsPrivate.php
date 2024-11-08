<?php

namespace App\Sys\Res\Types\Stk\Root\NsSysTypes\Ns;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\PrivateType;
use App\Sys\Res\Types\Stk\Root\NsSysTypes\ThisNsType;


class ThisNsPrivate extends BaseType
{

    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.types.private_type_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace private type uuid is not set in .env");
        }
        return $name;
    }
    const TYPE_NAME = 'system_private';

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;

    const PARENT_CLASSES = [
        ThisNsType::class,
        PrivateType::class,
    ];

}

