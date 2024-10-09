<?php

namespace App\Sys\Res\Servers;


use App\Enums\Server\TypeOfServerStatus;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;

interface ISystemServer extends ISystemResource
{
    public function getServerUuid() :string;
    public function getServerDomain() :string;
    public function getServerName() :string;
    public function getServerStatus() :TypeOfServerStatus;
    public function getServerNamespace() :?ISystemNamespace;


}
