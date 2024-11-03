<?php

namespace App\Sys\Res\Users\Stock;


use App\Exceptions\HexbatchInitException;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Users\BaseSystemUser;

class SystemUser extends BaseSystemUser
{

    const NAMESPACE_CLASS = ThisServerNamespace::class;


    public static function getClassUuid() : string {
        $name = config('hbc.system.user.uuid');
        if (!$name) {
            throw new HexbatchInitException("System user uuid is not set in .env");
        }
        return $name;
    }

    public static function getUserName() :string {
        $name = config('hbc.system.user.username');
        if (!$name) {
            throw new HexbatchInitException("System user name is not set in .env");
        }
        return $name;
    }
    public static function getUserPassword() :string {
        $pw = config('hbc.system.user.password');
        if (!$pw) {
            throw new HexbatchInitException("System user pw is not set in .env");
        }
        return $pw;
    }

}
