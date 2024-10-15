<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class NamespaceDestroyNoEvents extends Act\Cmd
{
    const UUID = '89059226-b860-4d62-9ff9-1d88a4b7037a';
    const ACTION_NAME = TypeOfAction::CMD_NAMESPACE_DESTROY_NO_EVENTS;
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

