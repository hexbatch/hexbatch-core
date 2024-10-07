<?php

namespace App\System\Resources\Users\Stock;


use App\Exceptions\HexbatchInitException;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Users\BaseSystemUser;

class SystemUser extends BaseSystemUser
{
    const UUID = '2e3bfcdc-ac5b-4229-8919-b5a9a67f7701';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

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

}
