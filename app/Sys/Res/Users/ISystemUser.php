<?php

namespace App\Sys\Res\Users;


use App\Models\User;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;

interface ISystemUser extends ISystemResource
{

    public static function getDictionaryObject() :ISystemUser;
    public static function getUserName() :string;
    public static function getUserPassword() :string;
    public function getUserNamespace() :?ISystemNamespace;

    public function getUserObject() : ?User;

    public static function getSystemNamespaceClass() :string|ISystemNamespace;

}
