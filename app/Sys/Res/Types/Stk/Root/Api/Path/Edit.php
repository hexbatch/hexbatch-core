<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Edit extends BaseType
{
    const UUID = 'adec0ce3-a6ca-4207-8726-71ae7556265b';
    const TYPE_NAME = 'api_path_edit';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Pa\PathEdit::class,
    ];

}

