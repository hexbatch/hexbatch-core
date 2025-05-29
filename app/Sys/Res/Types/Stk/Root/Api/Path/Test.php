<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Test extends Api\PathApi
{
    const UUID = 'b7265acf-ce8d-4330-a893-46e356b8f3ed';
    const TYPE_NAME = 'api_path_test';





    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Pa\PathTest::class,
    ];

}

