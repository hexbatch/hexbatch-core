<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;


class ElsewherePurge extends Act\Cmd
{
    const UUID = '71383481-1b7c-433d-9419-2b45152ab503';
    const ACTION_NAME = TypeOfAction::CMD_ELSEWHERE_PURGE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID,
        Act\CmdNoEvents::UUID
    ];

}

