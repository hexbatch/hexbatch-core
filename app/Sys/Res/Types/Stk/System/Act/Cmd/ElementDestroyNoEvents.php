<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class ElementDestroyNoEvents extends Act\Cmd
{
    const UUID = 'da1fda45-5a65-4e85-a8f4-86c1b334648f';
    const ACTION_NAME = TypeOfAction::CMD_ELEMENT_DESTROY_NO_EVENTS;
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

