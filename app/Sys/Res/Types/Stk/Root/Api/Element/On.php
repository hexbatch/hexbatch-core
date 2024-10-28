<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class On extends BaseType
{
    const UUID = 'b98ced27-eced-4348-8266-3920e5796b77';
    const TYPE_NAME = 'api_element_on';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ElementOn::class,
    ];

}

