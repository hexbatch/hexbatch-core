<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CopyLive extends Api\ElementApi
{
    const UUID = 'db0e2856-02f9-4b1b-9066-eb6651e72dfa';
    const TYPE_NAME = 'api_element_copy';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\LiveTypeCopy::class,
    ];

}

