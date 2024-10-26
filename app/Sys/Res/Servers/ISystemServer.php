<?php

namespace App\Sys\Res\Servers;


use App\Enums\Server\TypeOfServerStatus;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;

interface ISystemServer extends ISystemResource,IServer
{
    public function getServerStatus() :TypeOfServerStatus;
    public function getServerSystemNamespace() :?ISystemNamespace;


    public static function getServerDomain(): string;
    public static function getServerName(): string;
    public static function getSystemNamespaceClass(): string|ISystemNamespace;


}
