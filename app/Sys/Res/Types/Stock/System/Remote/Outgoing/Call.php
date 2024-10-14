<?php

namespace App\Sys\Res\Types\Stock\System\Remote\Outgoing;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Remote;

//this is the remote call made
class Call extends BaseType
{
    const UUID = '6eb52c93-1065-4266-87f8-e94a00541476';
    const TYPE_NAME = 'remote_outgoing_call';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote\Outgoing::UUID
    ];

}

