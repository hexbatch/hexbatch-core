<?php

namespace App\Sys\Res\Types\Stk\System\Act\Op;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\System\Act\Op;

/*
  * pop
    (p)A => B(+e) + A(-e)
     removes last to be added, with p doing different ordering, the p min and max control how many elements should be processed. B can be null.
     P can be null, only provide min and or max, or select the elements in which order will be popped
when an element is removed from its last set, it is automatically destroyed
 */
class Pop extends BaseType
{
    const UUID = '6c46ce70-59cc-4df5-84fc-2e281eb26ee4';
    const TYPE_NAME = 'op_pop';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Op::UUID
    ];

}

