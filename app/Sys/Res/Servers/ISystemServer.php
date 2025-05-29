<?php

namespace App\Sys\Res\Servers;


use App\Enums\Server\TypeOfServerStatus;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Types\ISystemType;

interface ISystemServer extends ISystemResource,IServer
{
    public static function getDictionaryObject() :ISystemServer;
    public function getServerStatus() :TypeOfServerStatus;
    public function getServerSystemNamespace() :?ISystemNamespace;


    public static function getServerDomain(): string;
    public static function getServerName(): string;
    public static function getServerUrl(): string;
    public static function getSystemNamespaceClass(): string|ISystemNamespace;
    public static function getSystemTypeClass(): string|ISystemType;

    public function getISystemServer(): ISystemServer;


}
