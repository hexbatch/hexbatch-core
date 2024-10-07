<?php

namespace App\System\Resources\Namespaces\Stock;


use App\Exceptions\HexbatchInitException;
use App\System\HexbatchResourceNotImplemented;
use App\System\Resources\ISystemResource;
use App\System\Resources\Namespaces\BaseSystemNamespace;
use App\System\Resources\Namespaces\ISystemNamespace;
use App\System\Resources\Users\BaseSystemUser;
use App\System\Resources\Users\Stock\SystemUser;

class SystemUserNamespace extends BaseSystemNamespace
{

    const UUID = 'd5eb0c72-1db8-4658-9615-3502ef724e51';
    const PUBLIC_ELEMENT_UUID = '';
    const PRIVATE_ELEMENT_UUID = '';
    const HOMESET_UUID = '';
    const ATTRIBUTE_UUID = '';
    const SERVER_UUID = '';
    const USER_UUID = SystemUser::UUID;

    public function getNamespaceName(): string
    {
        $name = config('hbc.system.namespace.system_name');
        if (!$name) {
            throw new HexbatchInitException("System namespace name is not set in .env");
        }
        return $name;
    }

    public function getNamespacePublicKey(): string
    {
        $name = config('hbc.system.namespace.system_public_key');
        if (!$name) {
            throw new HexbatchInitException("System namespace public key is not set in .env");
        }
        return $name;
    }
}
