<?php

namespace App\Sys\Res\Types\Stock\System\Act\Op;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Act\Op;

/*
 (p)A op (q)B => C
  OR, XOR, AND
  to remove then filter of some other thing that produces/returns a set

when an element is removed from its last set, it is automatically destroyed
 */
class Combine extends BaseType
{
    const UUID = 'c8833a43-8e2a-4a88-995f-f27c816dc073';
    const TYPE_NAME = 'op_combine';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Op::UUID
    ];

}

