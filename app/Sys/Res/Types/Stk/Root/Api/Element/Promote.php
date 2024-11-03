<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Promote extends Api\ElementApi
{
    const UUID = 'd1c5d047-dd64-401a-a80a-24bea97b0d72';
    const TYPE_NAME = 'api_element_promote';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\ElementPromote::class,
    ];

}

