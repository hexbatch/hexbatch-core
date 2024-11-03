<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Location extends Api\ElementApi
{
    const UUID = 'f6e8c607-f41f-409a-bfd8-68bc0988864b';
    const TYPE_NAME = 'api_element_read_location';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ReadLocation::class,
    ];

}

