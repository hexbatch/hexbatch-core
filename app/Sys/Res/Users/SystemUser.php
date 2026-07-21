<?php

namespace App\Sys\Res\Users;


use App\Exceptions\HexbatchInitException;

class SystemUser
{



    public static function getUserUuid() : string {
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
