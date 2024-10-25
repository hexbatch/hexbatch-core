<?php

namespace App\Sys\Res\Types\Stk\Root\Signal\Master;

use App\Sys\Res\Types\BaseType;


class ActionSetType extends BaseType
{
    const UUID = '0eb2212a-73e1-4215-99cb-008b831d785a';
    const TYPE_NAME = 'remote_rules_set';


    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        OuterSetType::class,
    ];

}

