<?php

namespace App\System\Resources\Servers\Stock;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchInitException;
use App\System\Collections\SystemServers;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Servers\BaseServer;


class ThisServer extends BaseServer {
    const UUID = '32998341-aa98-425e-b556-c342d029bb56';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

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
        return TypeOfServerStatus::ALLOWED;
    }

    public function onNextStep(): void
    {
        //set the uuid to the one in the config, and update the resource for the new uuid
        $this->server->ref_uuid = static::getThisServerUuid();
        $this->server->save();
        SystemServers::updateResourceGuid(static::UUID,$this->server->ref_uuid);
    }
}
