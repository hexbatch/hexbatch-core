<?php

namespace App\Sys\Res\Types\Stk\System\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;
use App\Sys\Res\Types\Stk\System\Evt;


class ElementOff extends Act\Pragma
{
    const UUID = '8d342e23-c7dc-475f-8d0b-26157ac28302';
    const ACTION_NAME = TypeOfAction::PRAGMA_ELEMENT_OFF;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const EVENT_UUIDS = [
        Evt\Set\ElementAttributeOff::UUID
    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

}

