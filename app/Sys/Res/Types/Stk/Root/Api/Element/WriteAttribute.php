<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class WriteAttribute extends Api\ElementApi
{
    const UUID = '26a090a2-708a-4c76-b387-08f537f0c2d5';
    const TYPE_NAME = 'api_element_write';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\Write::class,
    ];

}

