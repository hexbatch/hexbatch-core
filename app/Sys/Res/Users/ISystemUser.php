<?php

namespace App\Sys\Res\Users;


use App\Models\User;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;

interface ISystemUser extends ISystemResource
{

    public function getUserName() :string;
    public function getUserPassword() :string;
    public function getUserNamespace() :?ISystemNamespace;

    public function getUserObject() : ?User;

}
