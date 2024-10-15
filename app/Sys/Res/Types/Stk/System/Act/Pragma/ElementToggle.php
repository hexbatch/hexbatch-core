<?php

namespace App\Sys\Res\Types\Stk\System\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;
use App\Sys\Res\Types\Stk\System\Evt;

class ElementToggle extends Act\Pragma
{
    const UUID = '07b04351-6646-4445-af78-b0c61ef70929';
    const ACTION_NAME = TypeOfAction::PRAGMA_ELEMENT_TOGGLE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

    const EVENT_UUIDS = [
        Evt\Set\ElementAttributeOn::UUID,
        Evt\Set\ElementAttributeOff::UUID,
    ];

}

