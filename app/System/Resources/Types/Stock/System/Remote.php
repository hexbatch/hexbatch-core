<?php

namespace App\System\Resources\Types\Stock\System;



use App\System\Resources\Attributes\Stock\System\Remote\RemoteDataFormat;
use App\System\Resources\Attributes\Stock\System\Remote\RemoteHeader;
use App\System\Resources\Attributes\Stock\System\Remote\RemoteMethod;
use App\System\Resources\Attributes\Stock\System\Remote\RemoteProtocol;
use App\System\Resources\Attributes\Stock\System\Remote\RemoteUri;
use App\System\Resources\Attributes\Stock\System\Server\CommitHash;
use App\System\Resources\Attributes\Stock\System\Server\Version;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Remote extends BaseType
{
    const UUID = 'dc9ddf4b-3ac7-4ac8-9bf5-932e32e74b70';
    const TYPE_NAME = 'remote';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        RemoteDataFormat::UUID,
        RemoteHeader::UUID,
        RemoteMethod::UUID,
        RemoteProtocol::UUID,
        RemoteUri::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

