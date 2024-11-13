<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveHandle extends Api\TypeApi
{
    const UUID = 'ee60f8fe-85fc-404e-897e-8187b4720c76';
    const TYPE_NAME = 'api_type_remove_handle';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ty\TypeHandleRemove::class
    ];

}

