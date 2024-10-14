<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Act;


class RunRemote extends BaseType
{
    const UUID = '06c6d184-1230-4bd1-9ee4-80657a9e3620';
    const TYPE_NAME = 'cmd_run_remote';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

