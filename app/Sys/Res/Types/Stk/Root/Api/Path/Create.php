<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Create extends Api\PathApi
{
    const UUID = 'e91a71e6-14a7-4315-ae63-4e3b1a3b22dc';
    const TYPE_NAME = 'api_path_create';





    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Pa\PathCreate::class,
    ];

}

