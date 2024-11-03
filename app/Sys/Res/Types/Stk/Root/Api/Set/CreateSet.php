<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateSet extends Api\SetApi
{
    const UUID = '7255ea40-d9f7-40d3-87c8-442269c77c96';
    const TYPE_NAME = 'api_set_create';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetCreate::class,
    ];

}

