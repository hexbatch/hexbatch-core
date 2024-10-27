<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends BaseType
{
    const UUID = '9e70edf8-19d9-4b38-b552-e0013ad55e61';
    const TYPE_NAME = 'api_element_purge';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\ElementPurge::class,
    ];

}

