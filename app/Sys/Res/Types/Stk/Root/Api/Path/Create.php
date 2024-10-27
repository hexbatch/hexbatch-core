<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Create extends BaseType
{
    const UUID = 'e91a71e6-14a7-4315-ae63-4e3b1a3b22dc';
    const TYPE_NAME = 'api_path_create';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\PathCreate::class,
    ];

}

