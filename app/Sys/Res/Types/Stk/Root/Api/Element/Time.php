<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Time extends Api\ElementApi
{
    const UUID = 'b768f6e4-e9e6-489b-9bf8-df322cebfa21';
    const TYPE_NAME = 'api_element_read_time';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ReadTimeSpan::class,
    ];

}

