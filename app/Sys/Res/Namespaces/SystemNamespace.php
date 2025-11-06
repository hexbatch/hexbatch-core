<?php

namespace App\Sys\Res\Namespaces;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Servers\ThisServer;

/**
 * @see ThisServer
 */
class SystemNamespace
{

    public static function isDefault() : bool { return true;}

    public static function getNamespaceName(): string
    {
        $name = config('hbc.system.namespace.name');
        if (!$name) {
            throw new HexbatchInitException("System namespace name is not set in .env");
        }
        return $name;
    }

    public static function getNamespaceUuid() : string {
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

    public static function getBaseTypeUuid() : string {
        $name = config('hbc.system.namespace.base_type_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace type uuid is not set in .env");
        }
        return $name;
    }

    public static function getPublicElementUuid() : string {
        $name = config('hbc.system.namespace.elements_and_sets.public_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace public element uuid is not set in .env");
        }
        return $name;
    }

    public static function getPrivateElementUuid() : string {
        $name = config('hbc.system.namespace.elements_and_sets.private_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace private element uuid is not set in .env");
        }
        return $name;
    }

    public static function getHomeSetElementUuid() : string {
        $name = config('hbc.system.namespace.elements_and_sets.home_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace home set element uuid is not set in .env");
        }
        return $name;
    }

    public static function getHomeSetUuid() : string {
        $name = config('hbc.system.namespace.elements_and_sets.set_uuid');
        if (!$name) {
            throw new HexbatchInitException("System namespace home set uuid is not set in .env");
        }
        return $name;
    }


}
