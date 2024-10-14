<?php

namespace App\Sys\Res\Types\Stk\System\Remote\Outgoing;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Remote\RemoteSetType;


class RemoteCacheSetType extends BaseType
{
    const UUID = '93079736-8784-46a1-93ca-08c48a792dc8';
    const TYPE_NAME = 'remote_cache_set';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        RemoteSetType::UUID,
        RemoteCache::UUID,
    ];

}

