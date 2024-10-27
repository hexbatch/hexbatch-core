<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroyPart extends BaseType
{
    const UUID = '6633d1e7-c2d8-4f36-9762-5368d9aa249b';
    const TYPE_NAME = 'api_path_destroy_part';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\PathPartDestroy::class,
    ];

}

