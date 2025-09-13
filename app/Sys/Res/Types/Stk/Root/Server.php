<?php

namespace App\Sys\Res\Types\Stk\Root;



use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Server extends BaseType
{
    const UUID = '4c1a7519-0f23-4f0a-a168-1fabcbe2c1ec';
    const TYPE_NAME = 'server';


    const ATTRIBUTE_CLASSES = [
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData::class,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\CommitHash::class,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\Version::class,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\Domain::class,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\AboutServer::class,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\HomeUrl::class,
    ];

    const PARENT_CLASSES = [
        Root::class
    ];


}

