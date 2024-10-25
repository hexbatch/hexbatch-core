<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Other;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Other;


class PrivateType extends BaseType
{
    const UUID = 'a5529dd2-833a-4310-bcff-adef52bedb09';
    const TYPE_NAME = 'ther_private';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Other::class,
        \App\Sys\Res\Types\Stk\Root\Namespace\PrivateType::class
    ];

}

