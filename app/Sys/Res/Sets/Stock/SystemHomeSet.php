<?php

namespace App\Sys\Res\Sets\Stock;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemHomeSetElement;
use App\Sys\Res\Ele\Stk\SystemNS\SystemPrivateElement;
use App\Sys\Res\Ele\Stk\SystemNS\SystemNSPublicElement;
use App\Sys\Res\Sets\BaseSet;

class SystemHomeSet extends BaseSet
{
    public static function getClassUuid() : string {
        $name = config('hbc.system.namespace.elements_and_sets.set_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace set uuid is not set in .env");
        }
        return $name;
    }
    const ELEMENT_CLASS = SystemHomeSetElement::class;

    const CONTAINING_ELEMENT_CLASSES = [

        SystemPrivateElement::class,
        SystemNSPublicElement::class,
    ];

}



