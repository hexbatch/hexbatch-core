<?php

namespace App\Sys\Res\Types\Stock\System\Remote\Incoming;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Remote;

//this is the remote response
class Response extends BaseType
{
    const UUID = '574388c8-0bec-4e11-aa62-2008f8e179ef';
    const TYPE_NAME = 'remote_incoming_response';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote\Incoming::class
    ];

}

