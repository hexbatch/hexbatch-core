<?php

namespace App\Sys\Res\Servers;


use App\Models\Server;
use App\Sys\Res\Namespaces\INamespace;

interface IServer
{
    public function getServerUuid() :string;
    public function getServerDomain() :string;
    public function getServerName() :string;

    public function getServerNamespaceInterface() :?INamespace;
    public function getServerObject() :?Server;


}
