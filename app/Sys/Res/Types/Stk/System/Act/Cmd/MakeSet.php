<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class MakeSet extends Act\Cmd
{
    const UUID = '06c6d184-1230-4bd1-9ee4-80657a9e3620';
    const TYPE_NAME = TypeOfAction::COMMAND_MAKE_SET;
    const ACTION_NAME = TypeOfAction::COMMAND_MAKE_SET;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

