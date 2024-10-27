<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class WriteVisual extends BaseType
{
    const UUID = 'e64dffe7-9934-47bf-bbda-255c57e83845';
    const TYPE_NAME = 'api_element_write_visual';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Pragma\Search::class,
        Act\Pragma\WriteVisual::class,
    ];

}

