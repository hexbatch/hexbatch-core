<?php

namespace App\Sys\Res\Servers;


use App\Models\Server;

interface IServer
{

    public function getServerObject() :?Server;


}
