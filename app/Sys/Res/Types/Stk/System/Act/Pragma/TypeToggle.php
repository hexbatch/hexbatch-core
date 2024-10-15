<?php

namespace App\Sys\Res\Types\Stk\System\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;
use App\Sys\Res\Types\Stk\System\Evt;

class TypeToggle extends Act\Pragma
{
    const UUID = 'ff83a44d-898e-4a87-9e29-0d544f684b3b';
    const ACTION_NAME = TypeOfAction::PRAGMA_TYPE_TOGGLE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

    const EVENT_UUIDS = [
        Evt\Set\ElementTypeOff::UUID,
        Evt\Set\ElementTypeOn::UUID,
    ];

}

