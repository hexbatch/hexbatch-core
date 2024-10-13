<?php

namespace App\Sys\Res\Types\Stock\System\Act\Op;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Act\Op;

/*
   * unshift adds e to the first of the set, the p min and max control how many elements should be processed.
    (p) -> A(-e)
when an element is removed from its last set, it is automatically destroyed
 */

class Unshift extends BaseType
{
    const UUID = 'c4f79042-3be1-4c9a-9342-235341d5f0d0';
    const TYPE_NAME = 'op_unshift';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Op::UUID
    ];

}

