<?php

namespace App\Sys\Res\Types\Stk\Root;



use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Server extends BaseType
{
    const UUID = '4c1a7519-0f23-4f0a-a168-1fabcbe2c1ec';
    const TYPE_NAME = 'server';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\CommitHash::UUID,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\Version::UUID,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\Domain::UUID,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\About::UUID,
        \App\Sys\Res\Atr\Stk\MetaData\Server\ServerData\HomeUrl::UUID,
    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}

