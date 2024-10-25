<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Other;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Other;


class PublicType extends BaseType
{
    const UUID = '93d19ac0-dfd9-4c56-b2d9-96887505c7e2';
    const TYPE_NAME = 'other_public';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Other::class,
        \App\Sys\Res\Types\Stk\Root\Namespace\PublicType::class
    ];

}

