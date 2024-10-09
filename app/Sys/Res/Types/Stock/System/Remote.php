<?php

namespace App\Sys\Res\Types\Stock\System;



use App\Sys\Res\Attributes\Stock\System\Remote\RemoteDataFormat;
use App\Sys\Res\Attributes\Stock\System\Remote\RemoteHeader;
use App\Sys\Res\Attributes\Stock\System\Remote\RemoteMethod;
use App\Sys\Res\Attributes\Stock\System\Remote\RemoteProtocol;
use App\Sys\Res\Attributes\Stock\System\Remote\RemoteUri;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Remote extends BaseType
{
    const UUID = 'dc9ddf4b-3ac7-4ac8-9bf5-932e32e74b70';
    const TYPE_NAME = 'remote';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

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

