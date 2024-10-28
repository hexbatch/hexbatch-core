<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveHandle extends BaseType
{
    const UUID = 'bb2ede95-c699-442e-a3b8-8555b6937567';
    const TYPE_NAME = 'api_path_remove_handle';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Pa\PathHandleRemove::class,
    ];

}

