<?php

namespace App\System\Resources\Types\Stock\System;



use App\System\Resources\Attributes\Stock\System\Server\CommitHash;
use App\System\Resources\Attributes\Stock\System\Server\Version;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Server extends BaseType
{
    const UUID = '4c1a7519-0f23-4f0a-a168-1fabcbe2c1ec';
    const TYPE_NAME = 'server';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        CommitHash::UUID,
        Version::UUID,
        CommitHash::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

