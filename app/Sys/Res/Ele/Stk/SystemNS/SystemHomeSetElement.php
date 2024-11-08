<?php

namespace App\Sys\Res\Ele\Stk\SystemNS;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\BaseElement;
use App\Sys\Res\Types\Stk\Root\NsSysTypes\Ns\ThisNsHomeset;

class SystemHomeSetElement extends BaseElement
{
    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.elements_and_sets.home_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace home set element uuid is not set in .env");
        }
        return $name;
    }

    const TYPE_CLASS = ThisNsHomeset::class;

}


