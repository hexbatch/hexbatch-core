<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Ping extends BaseType
{
    const UUID = 'b102b51c-862d-466f-8eb6-a8ec1bc421d0';
    const TYPE_NAME = 'api_element_ping';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ElementPing::class,
    ];

}

