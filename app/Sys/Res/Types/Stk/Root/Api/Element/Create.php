<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Create extends Api\ElementApi
{
    const UUID = 'bad981d1-f817-4f89-879c-3d2d9c6443b6';
    const TYPE_NAME = 'api_element_create';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ElementCreate::class,
    ];

}

