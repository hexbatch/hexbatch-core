<?php

namespace App\System\Resources\Servers;


use App\Enums\Server\TypeOfServerStatus;
use App\System\Resources\Namespaces\ISystemNamespace;

interface ISystemServer
{
    public function getServerUuid() :string;
    public function getServerDomain() :string;
    public function getServerName() :string;
    public function getServerStatus() :TypeOfServerStatus;
    public function getServerNamespace() :ISystemNamespace;


}
