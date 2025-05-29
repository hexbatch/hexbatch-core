<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DemoteLive extends Api\ElementApi
{
    const UUID = '80de40b0-c682-447f-b7a2-a0e4cf20faf4';
    const TYPE_NAME = 'api_element_demote_live';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ele\LiveTypeDemote::class,
    ];

}

