<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Server;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ShowAdmin extends Api\ServerApi
{
    const UUID = '6ddab2ac-2855-455a-b46a-79d116e6918b';
    const TYPE_NAME = 'api_server_show_admin';





    const PARENT_CLASSES = [
        Api\ServerApi::class,
        Act\Cmd\Server\ServerShowAdmin::class,
    ];

}

