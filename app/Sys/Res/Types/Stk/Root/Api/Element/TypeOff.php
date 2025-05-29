<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TypeOff extends Api\ElementApi
{
    const UUID = '2a8f43d7-62b1-4776-9868-42a31de9035d';
    const TYPE_NAME = 'api_element_type_off';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\TypeOff::class,
    ];

}

