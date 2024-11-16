<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Destroy extends Api\ElementApi
{
    const UUID = 'bd9d7481-5f47-4bd6-8ec0-90f4df0c91be';
    const TYPE_NAME = 'api_element_destroy';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\ElementDestroy::class,
    ];

}

