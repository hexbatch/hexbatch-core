<?php

namespace App\Sys\Res\Servers\Stock;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\Sys\Collections\SystemServers;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Servers\BaseServer;


class ThisServer extends BaseServer {

    const NAMESPACE_CLASS = ThisServerNamespace::class;

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

    public static function getServerName(): string
    {
        $name = config('hbc.system.server.name');
        if (!$name) {
            throw new HexbatchInitException("Server name is not set in .env");
        }
        return $name;
    }

    public function getServerStatus(): TypeOfServerStatus
    {
        return TypeOfServerStatus::ALLOWED_SERVER;
    }

    public function onNextStep(): void
    {
        //set the uuid to the one in the config, and update the resource for the new uuid
        $this->server->ref_uuid = static::getClassUuid();
        $this->server->save();
        SystemServers::updateResourceGuid(static::class,$this->server->ref_uuid);
    }
}
