<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Destroy extends BaseType
{
    const UUID = '95bd9a33-bd70-4c42-8b10-80ea6aabb19e';
    const TYPE_NAME = 'api_path_destroy';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Pa\PathDestroy::class,
    ];

}

