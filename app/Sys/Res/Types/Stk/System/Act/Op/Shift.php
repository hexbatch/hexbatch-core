<?php

namespace App\Sys\Res\Types\Stk\System\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;

/*
  * shift
    removes from first to be added, the p min and max control how many elements should be processed.B can be null.
    P can be null, only provide min and or max, or select the elements in which order will be shifted
    (p)A -> B(+e) + A(-e)
 */

class Shift extends Act\Op
{
    const UUID = '917a84ec-c17c-40d3-b218-da35edc62ac6';
    const TYPE_NAME = TypeOfAction::OP_SHIFT;
    const ACTION_NAME = TypeOfAction::OP_SHIFT;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Op::UUID
    ];

}

