<?php

namespace App\Sys\Res\Types\Stk\Root\NsSysTypes;

use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHandleElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;

class ThisNsType extends BaseType
{
    const TYPE_NAME = 'system_namespace';

    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.types.ns_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace type uuid is not set in .env");
        }
        return $name;
    }

    const HANDLE_ELEMENT_CLASS = SystemHandleElement::class;


    const PARENT_CLASSES = [
        BasePerNamespace::class,
    ];


}

