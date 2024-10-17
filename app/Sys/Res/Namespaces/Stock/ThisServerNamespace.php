<?php

namespace App\Sys\Res\Namespaces\Stock;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Ele\Stk\SystemNSElements\SystemPrivateElement;
use App\Sys\Res\Ele\Stk\SystemNSElements\SystemPublicElement;
use App\Sys\Res\Namespaces\BaseNamespace;
use App\Sys\Res\Servers\Stock\ThisServer;
use App\Sys\Res\Sets\Stock\SystemHomeSet;
use App\Sys\Res\Types\Stk\Root\SystemNamespaceTypes\System\ThisServer\ThisServerNS;
use App\Sys\Res\Users\Stock\SystemUser;

class ThisServerNamespace extends BaseNamespace
{

    //todo each server's namespace uuid is different, change this constant below to a method, and get the uuid from the .env or make a new one
    const UUID = 'd5eb0c72-1db8-4658-9615-3502ef724e51';

    const TYPE_UUID = ThisServerNS::UUID;
    const PUBLIC_ELEMENT_UUID = SystemPublicElement::UUID;
    const PRIVATE_ELEMENT_UUID = SystemPrivateElement::UUID;
    const HOMESET_UUID = SystemHomeSet::UUID;
    const SERVER_UUID = ThisServer::UUID;
    const USER_UUID = SystemUser::UUID;

    public function getNamespaceName(): string
    {
        $name = config('hbc.system.namespace.system_name');
        if (!$name) {
            throw new HexbatchInitException("System namespace name is not set in .env");
        }
        return $name;
    }

    public function getNamespacePublicKey(): ?string
    {
        $name = config('hbc.system.namespace.system_public_key');
        if (!$name) {
            throw new HexbatchInitException("System namespace public key is not set in .env");
        }
        return $name;
    }
}
