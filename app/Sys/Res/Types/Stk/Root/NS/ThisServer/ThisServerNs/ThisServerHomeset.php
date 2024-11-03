<?php

namespace App\Sys\Res\Types\Stk\Root\NS\ThisServer\ThisServerNs;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\HomeSet;
use App\Sys\Res\Types\Stk\Root\NS\ThisServer\ThisServerNS;


class ThisServerHomeset extends BaseType
{

    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.types.homeset_type_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace home set type uuid is not set in .env");
        }
        return $name;
    }

    const TYPE_NAME = 'system_homeset';

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;

    const PARENT_CLASSES = [
        ThisServerNS::class,
        HomeSet::class
    ];

}

