<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class SetDestroyNoEvents extends Act\Cmd
{
    const UUID = 'd0d23dc0-d588-4a51-b10b-b2f3a8cfd49a';
    const ACTION_NAME = TypeOfAction::CMD_SET_DESTROY_NO_EVENTS;
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

