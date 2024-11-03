<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Publish extends Api\PathApi
{
    const UUID = '81a4a17c-571d-42eb-8f87-31dbdb526f7d';
    const TYPE_NAME = 'api_path_publish';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\Pa\PathPublish::class,
    ];

}

