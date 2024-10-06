<?php

namespace App\System\Resources\Users;


use App\Models\User;
use App\System\Resources\ISystemResource;
use App\System\Resources\Namespaces\ISystemNamespace;

interface ISystemUser extends ISystemResource
{
    public function getUserUuid() :string;
    public function getUserName() :string;
    public function getUserPassword() :string;
    public function getUserNamespace() :ISystemNamespace;

    public function getUserObject() : User;

}
