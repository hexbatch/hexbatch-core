<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreatePart extends Api\PathApi
{
    const UUID = 'f7675c29-0a0b-4615-8e81-710b3a111ad9';
    const TYPE_NAME = 'api_path_create_part';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Pa\PathPartCreate::class,
    ];

}

