<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateSet extends Api\SetApi
{
    const UUID = '7255ea40-d9f7-40d3-87c8-442269c77c96';
    const TYPE_NAME = 'api_element_create_set';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\SetCreate::class,
    ];

}

