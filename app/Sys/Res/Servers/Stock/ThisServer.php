<?php

namespace App\Sys\Res\Servers\Stock;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\Sys\Collections\SystemServers;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Servers\BaseServer;


class ThisServer extends BaseServer {
    //todo each server uuid is different, change this constant below to a method, and get the uuid from the .env or make a new one
    const UUID = '32998341-aa98-425e-b556-c342d029bb56';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    public static function getThisServerUuid() : string {
        $name = config('hbc.system.server.server_uuid');
        if (!$name) {
            throw new HexbatchInitException("Server uuid is not set in .env");
        }
        return $name;
    }

    public function getServerUuid() :string {
        return static::getThisServerUuid();
    }

    public function getServerDomain(): string
    {
        $name = config('hbc.system.server.server_domain');
        if (!$name) {
            throw new HexbatchInitException("Server domain is not set in .env");
        }
        return $name;
    }

    public function getServerName(): string
    {
        $name = config('hbc.system.server.server_name');
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
        $this->server->ref_uuid = static::getThisServerUuid();
        $this->server->save();
        SystemServers::updateResourceGuid(static::UUID,$this->server->ref_uuid);
    }
}
