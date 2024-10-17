<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Pragma;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Evt;


class WriteIncrement extends Act\Pragma
{
    const UUID = '1f571670-8aa9-42a8-a26c-5461f37f5178';
    const ACTION_NAME = TypeOfAction::PRAGMA_WRITE_INCREMENT;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Pragma::UUID
    ];

    const EVENT_UUIDS = [
        Evt\Set\Write::UUID
    ];

}

