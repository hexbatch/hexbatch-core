<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ReadLiveType extends Api\ElementApi
{
    const UUID = 'f39103d9-fd1d-4216-a376-26b0106d4315';
    const TYPE_NAME = 'api_element_read_live_type';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\ReadLiveType::class,
    ];

}

