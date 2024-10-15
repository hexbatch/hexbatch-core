<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Act;

/*
 (p) => M
    finds all the sets that share elements, (with path restricting the set/element chosen)
     M is a set without events
 */

class Mutual extends Act\Op
{
    const UUID = '7d52ebfa-079c-4a2d-9bef-874a473c5220';
    const ACTION_NAME = TypeOfAction::OP_MUTUAL;
    const TYPE_NAME = self::ACTION_NAME;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Op::UUID,
        Act\CmdNoSideEffects::UUID
    ];

}

