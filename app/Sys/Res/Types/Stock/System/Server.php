<?php

namespace App\Sys\Res\Types\Stock\System;



use App\Sys\Res\Attributes\Stock\System\MetaData\Server\ServerData;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;

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
        ServerData\CommitHash::UUID,
        ServerData\Version::UUID,
        ServerData\Domain::UUID,
        ServerData\About::UUID,
        ServerData\HomeUrl::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

