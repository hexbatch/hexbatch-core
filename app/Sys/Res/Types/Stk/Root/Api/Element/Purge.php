<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends Api\ElementApi
{
    const UUID = '9e70edf8-19d9-4b38-b552-e0013ad55e61';
    const TYPE_NAME = 'api_element_purge';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\ElementPurge::class,
    ];

}

