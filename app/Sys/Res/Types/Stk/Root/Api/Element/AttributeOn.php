<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AttributeOn extends Api\ElementApi
{
    const UUID = 'b98ced27-eced-4348-8266-3920e5796b77';
    const TYPE_NAME = 'api_element_on';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ElementOn::class,
    ];

}

