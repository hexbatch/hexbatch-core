<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Add extends BaseType
{
    const UUID = 'e5c47fc2-e128-4912-b546-6d78b0420f90';
    const TYPE_NAME = 'api_element_add';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\LiveTypeAdd::class,
    ];

}

