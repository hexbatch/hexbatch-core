<?php

namespace App\Sys\Res\Types\Stock\System\Remote;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Remote;


class Outgoing extends BaseType
{
    const UUID = 'ef367400-78fc-4460-a71c-3cf34c8e339d';
    const TYPE_NAME = 'remote_outgoing';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote::class
    ];

}

