<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class UnstickElement extends BaseType
{
    const UUID = 'b229bd56-9bab-4716-9cc2-e18ea93f8f29';
    const TYPE_NAME = 'api_set_unstick_element';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetMemberUnstick::class,
    ];

}

