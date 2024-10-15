<?php

namespace App\Sys\Res\Types\Stk\System\Act\Cmd;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;

/*
 * The live type attached is also added to the target(s) with the live type's attribute values
 */
class LiveTypeCopy extends Act\Cmd
{
    const UUID = '49390d1b-5ed0-49ea-9867-9615c2a1235e';
    const ACTION_NAME = TypeOfAction::CMD_LIVE_TYPE_COPY;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Cmd::UUID
    ];

}

