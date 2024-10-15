<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeDestroyNoEvents extends Act\Cmd
{
    const UUID = 'bd328b7f-1cbb-49e9-a127-451834dd98e6';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_DESTROY_NO_EVENTS;
    const TYPE_NAME = self::ACTION_NAME;

    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID,
        Act\CmdNoEvents::UUID,
    ];

}

