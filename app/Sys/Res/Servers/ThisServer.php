<?php

namespace App\Sys\Res\Servers;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Types\Stk\Root\Server\ThisServerType;


class ThisServer {


    public static function getServerTypeUUID() : string {
        return ThisServerType::getTypeUuid();
    }

    public static function getServerUuid() : string {
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

    public static function getServerStatus(): TypeOfServerStatus
    {
        return TypeOfServerStatus::ALLOWED_SERVER;
    }

}
