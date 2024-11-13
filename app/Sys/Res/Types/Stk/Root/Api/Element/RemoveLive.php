<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveLive extends Api\ElementApi
{
    const UUID = '18f455f3-39b0-4c84-92e1-21eb6af0236d';
    const TYPE_NAME = 'api_element_subtract';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ele\LiveTypeRemove::class,
    ];

}

