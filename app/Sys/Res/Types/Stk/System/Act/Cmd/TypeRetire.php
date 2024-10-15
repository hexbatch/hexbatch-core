<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;


class TypeRetire extends Act\Cmd
{
    const UUID = '4e99584e-0e22-4db6-a5c0-b99d1caa9c04';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_RETIRE;
    const TYPE_NAME = self::ACTION_NAME;

    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

