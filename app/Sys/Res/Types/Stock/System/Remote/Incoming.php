<?php

namespace App\Sys\Res\Types\Stock\System\Remote;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Remote;


class Incoming extends BaseType
{
    const UUID = '3af1261b-1159-4926-91f2-21778f40d324';
    const TYPE_NAME = 'incoming_remote';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Remote::class
    ];

}

