<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class Write extends Act\Pragma
{
    const UUID = '51e9a358-c2b1-4876-a518-0ab65d1be224';
    const ACTION_NAME = TypeOfAction::PRAGMA_WRITE;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

    const EVENT_UUIDS = [
        Evt\Set\AttributeWrite::UUID
    ];

}

