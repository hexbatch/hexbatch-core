<?php

namespace App\Sys\Res\Ele\Stk\SystemNS;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\BaseElement;
use App\Sys\Res\Types\Stk\Root\NS\System\ThisServer\ThisServerNs\ThisServerPrivate;

class SystemPrivateElement extends BaseElement
{
    public static function getUuid() : string {
        $name = config('hbc.system.namespace.private_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace private element uuid is not set in .env");
        }
        return $name;
    }
    const TYPE_CLASS = ThisServerPrivate::class;

}


