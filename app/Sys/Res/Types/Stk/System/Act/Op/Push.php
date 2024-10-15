<?php

namespace App\Sys\Res\Types\Stk\System\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;
use App\Sys\Res\Types\Stk\System\Evt;
/*
  * push adds e to the last of the set,the p min and max control how many elements should be processed.
    (p) -> A(+e)
 */

class Push extends Act\Op
{
    const UUID = 'ae5cf895-fee6-4042-93d2-ce83cfa77d05';
    const ACTION_NAME = TypeOfAction::OP_PUSH;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Op::UUID
    ];

    const EVENT_UUIDS = [
        Evt\Set\SetEnter::UUID,
    ];

}

