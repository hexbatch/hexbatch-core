<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Copy extends BaseType
{
    const UUID = 'bf0ad284-d97f-4711-9cb8-9b1568cf4a4f';
    const TYPE_NAME = 'api_path_copy';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\PathCopy::class,
    ];

}

