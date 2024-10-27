<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddHandle extends BaseType
{
    const UUID = '499a617f-ccb7-427a-83b9-c2d77cc9a1b3';
    const TYPE_NAME = 'api_path_add_handle';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\PathHandleAdd::class,
    ];

}

