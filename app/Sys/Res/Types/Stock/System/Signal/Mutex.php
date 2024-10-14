<?php

namespace App\Sys\Res\Types\Stock\System\Signal;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Signal;


class Mutex extends BaseType
{
    const UUID = 'ce614965-912f-4e57-b866-2d3fd73ff000';
    const TYPE_NAME = 'signal_mutex';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Signal::UUID
    ];

}

