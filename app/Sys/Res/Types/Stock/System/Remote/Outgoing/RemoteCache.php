<?php

namespace App\Sys\Res\Types\Stock\System\Remote\Outgoing;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Remote;

//this is the remote call made
class RemoteCache extends BaseType
{
    const UUID = '83557e7e-f7ab-433c-81c6-7d5e80cfd144';
    const TYPE_NAME = 'remote_outgoing_cache';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote\Outgoing::UUID
    ];

}

