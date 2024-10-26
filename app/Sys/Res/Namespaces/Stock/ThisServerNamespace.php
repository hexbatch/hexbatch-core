<?php

namespace App\Sys\Res\Namespaces\Stock;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNS\SystemNSPublicElement;
use App\Sys\Res\Ele\Stk\SystemNS\SystemPrivateElement;
use App\Sys\Res\Namespaces\BaseNamespace;
use App\Sys\Res\Servers\Stock\ThisServer;
use App\Sys\Res\Sets\Stock\SystemHomeSet;
use App\Sys\Res\Types\Stk\Root\NS\System\ThisServer\ThisServerNS;

/**
 * @see ThisServer
 */
class ThisServerNamespace extends BaseNamespace
{

    const TYPE_CLASS = ThisServerNS::class;
    const PUBLIC_ELEMENT_CLASS = SystemNSPublicElement::class;
    const PRIVATE_ELEMENT_CLASS = SystemPrivateElement::class;
    const HOMESET_CLASS = SystemHomeSet::class;

    public static function getNamespaceName(): string
    {
        $name = config('hbc.system.namespace.name');
        if (!$name) {
            throw new HexbatchInitException("System namespace name is not set in .env");
        }
        return $name;
    }

    public static function getUuid() : string {
        $name = config('hbc.system.namespace.uuid');
        if (!$name) {
            throw new HexbatchInitException("System user uuid is not set in .env");
        }
        return $name;
    }

    public static function getNamespacePublicKey(): ?string
    {
        $name = config('hbc.system.namespace.public_key');
        if (!$name) {
            throw new HexbatchInitException("System namespace public key is not set in .env");
        }
        return $name;
    }


}
