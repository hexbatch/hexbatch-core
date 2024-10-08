<?php

namespace App\System\Resources\Types\Stock\System;



use App\System\Resources\Attributes\Stock\System\ServerCommitHash;
use App\System\Resources\Attributes\Stock\System\ServerVersion;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class ServerType extends BaseType
{
    const UUID = '4c1a7519-0f23-4f0a-a168-1fabcbe2c1ec';
    const TYPE_NAME = 'server';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        ServerCommitHash::UUID,
        ServerVersion::UUID,
        ServerCommitHash::UUID,
    ];

    const PARENT_UUIDS = [];

}

