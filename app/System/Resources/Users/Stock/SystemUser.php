<?php

namespace App\System\Resources\Users\Stock;


use App\Exceptions\HexbatchInitException;
use App\System\HexbatchResourceNotImplemented;
use App\System\Resources\ISystemResource;
use App\System\Resources\Namespaces\ISystemNamespace;
use App\System\Resources\Users\BaseSystemUser;

class SystemUser extends BaseSystemUser
{
    const UUID = '2e3bfcdc-ac5b-4229-8919-b5a9a67f7701';

    protected ?ISystemNamespace $namespace = null;
    public function getUserUuid() :string { return static::UUID;}
    public function getUserName() :string {
        $name = config('hbc.system.system_user_name');
        if (!$name) {
            throw new HexbatchInitException("System user name is not set in .env");
        }
        return $name;
    }
    public function getUserPassword() :string {
        $pw = config('hbc.system.system_user_password');
        if (!$pw) {
            throw new HexbatchInitException("System user pw is not set in .env");
        }
        return $pw;
    }
    public function getUserNamespace() :ISystemNamespace {
        //todo gen new namespace by having its uuid constant and calling the system namespaces
        throw new HexbatchResourceNotImplemented();
    }

    public function onCall(): ISystemResource
    {
        $this->getUserObject();
        return $this;
    }
}
