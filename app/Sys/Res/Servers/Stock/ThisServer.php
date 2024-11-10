<?php

namespace App\Sys\Res\Servers\Stock;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Servers\BaseServer;
use App\Sys\Res\Types\Stk\Root\NsSysTypes\Server\ThisServerType;


class ThisServer extends BaseServer {

    const NAMESPACE_CLASS = ThisNamespace::class;
    const SERVER_TYPE_CLASS = ThisServerType::class;

    public static function getClassUuid() : string {
        $name = config('hbc.system.server.uuid');
        if (!$name) {
            throw new HexbatchInitException("Server user uuid is not set in .env");
        }
        return $name;
    }



    public static function getServerDomain(): string
    {
        $name = config('hbc.system.server.domain');
        if (!$name) {
            throw new HexbatchInitException("Server domain is not set in .env");
        }
        return $name;
    }

    public static function getClassName() : string { return static::getServerName();}
    public static function getServerName(): string
    {
        $name = config('hbc.system.server.name');
        if (!$name) {
            throw new HexbatchInitException("Server name is not set in .env");
        }
        return $name;
    }

    public static function getServerUrl(): string
    {
        $name = config('hbc.system.server.url');
        if (!$name) {
            throw new HexbatchInitException("Server url is not set in .env");
        }
        return $name;
    }

    public function getServerStatus(): TypeOfServerStatus
    {
        return TypeOfServerStatus::ALLOWED_SERVER;
    }

}
