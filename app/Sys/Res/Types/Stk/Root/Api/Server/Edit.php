<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Server;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Edit extends Api\ServerApi
{
    const UUID = '9c34e359-8927-43b9-8019-2ce2ab1e99d9';
    const TYPE_NAME = 'api_server_edit';





    const PARENT_CLASSES = [
        Api\ServerApi::class,
        Act\Cmd\Server\ServerEdit::class,
    ];

}

