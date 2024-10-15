<?php

namespace App\Sys\Res\Types\Stk\System\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;
use App\Sys\Res\Types\Stk\System\Evt;

/*
 (p)A op (q)B => C
  OR, XOR, AND
  to remove then filter of some other thing that produces/returns a set

when an element is removed from its last set, it is automatically destroyed
 */
class Combine extends Act\Op
{
    const UUID = 'c8833a43-8e2a-4a88-995f-f27c816dc073';
    const ACTION_NAME = TypeOfAction::OP_COMBINE;
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
        Evt\Set\SetLeave::UUID,
    ];

}

