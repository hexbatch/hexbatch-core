<?php

namespace App\Sys\Res\Types\Stk\System\Act\Op;

use App\Enums\Sys\TypeOfAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\System\Act;

/*
 (p)A op (q)B => C
  OR, XOR, AND
  to remove then filter of some other thing that produces/returns a set

when an element is removed from its last set, it is automatically destroyed
 */
class Combine extends Act\Op
{
    const UUID = 'c8833a43-8e2a-4a88-995f-f27c816dc073';
    const TYPE_NAME = TypeOfAction::OP_COMBINE;
    const ACTION_NAME = TypeOfAction::OP_COMBINE;
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Act\Op::UUID
    ];

}

