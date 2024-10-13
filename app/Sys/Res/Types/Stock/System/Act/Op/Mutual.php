<?php

namespace App\Sys\Res\Types\Stock\System\Act\Op;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Act\Op;

/*
 (p) => M
    finds all the sets that share elements, (with path restricting the set/element chosen)
     M is a set without events
 */

class Mutual extends BaseType
{
    const UUID = '7d52ebfa-079c-4a2d-9bef-874a473c5220';
    const TYPE_NAME = 'op_mutual';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Op::UUID
    ];

}

