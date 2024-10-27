<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Copy extends BaseType
{
    const UUID = 'db0e2856-02f9-4b1b-9066-eb6651e72dfa';
    const TYPE_NAME = 'api_element_copy';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\LiveTypeCopy::class,
    ];

}

