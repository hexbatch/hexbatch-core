<?php

namespace App\Sys\Res\Types\Stk\Root\NsSysTypes\Ns;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\PublicType;
use App\Sys\Res\Types\Stk\Root\NsSysTypes\ThisNsType;


class ThisNsPublic extends PublicType
{

    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.types.public_type_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace public type uuid is not set in .env");
        }
        return $name;
    }
    const TYPE_NAME = 'system_public';

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;

    const PARENT_CLASSES = [
        ThisNsType::class,
        PublicType::class
    ];

}

