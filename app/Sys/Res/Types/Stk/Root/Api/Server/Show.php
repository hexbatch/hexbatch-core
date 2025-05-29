<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Server;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Show extends Api\ServerApi
{
    const UUID = 'b22c2ec6-9f10-432c-b50b-4ac2e4c9448d';
    const TYPE_NAME = 'api_server_show';





    const PARENT_CLASSES = [
        Api\ServerApi::class,
        Act\Cmd\Server\ServerShow::class,
    ];

}

