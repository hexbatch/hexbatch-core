<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class TypeChangeOwner extends Act\Cmd
{
    const UUID = '997f8aba-30a0-4b14-a75e-c64dac02e85b';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_CHANGE_OWNER;
    const TYPE_NAME = self::ACTION_NAME;

    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

