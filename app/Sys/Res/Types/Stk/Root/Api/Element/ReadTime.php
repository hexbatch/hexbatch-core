<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ReadTime extends Api\ElementApi
{
    const UUID = 'd284a0c1-f730-4a1b-bd43-92bec784d481';
    const TYPE_NAME = 'api_element_read_time';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\ReadTimeSpan::class,
    ];

}

