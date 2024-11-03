<?php

namespace App\Sys\Res\Ele\Stk\SystemNS;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\BaseElement;
use App\Sys\Res\Types\Stk\Root\NS\ThisServer\ThisServerNs\ThisServerPublic;

class SystemNSPublicElement extends BaseElement
{
    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.elements_and_sets.public_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace public element uuid is not set in .env");
        }
        return $name;
    }
    const TYPE_CLASS = ThisServerPublic::class;

}


