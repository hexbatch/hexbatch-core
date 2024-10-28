<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveHandle extends BaseType
{
    const UUID = '21c06952-639d-4d3a-b189-9264b04ba0d0';
    const TYPE_NAME = 'api_set_remove_handle';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetChildHandleRemove::class,
    ];

}

