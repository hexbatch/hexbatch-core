<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;


class TypeCreate extends Act\Cmd
{
    const UUID = 'f635c4b8-5903-4688-802c-c0b28f376be0';
    const ACTION_NAME = TypeOfAction::CMD_TYPE_CREATE;
    const TYPE_NAME = self::ACTION_NAME;

    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

