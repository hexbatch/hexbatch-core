<?php

namespace App\Sys\Res\Types\Stock\System\Signal;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Signal;


class Semaphore extends BaseType
{
    const UUID = '635d3b10-55bf-4528-9d86-673b3fdc7211';
    const TYPE_NAME = 'signal_semaphore';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Signal::UUID
    ];

}

