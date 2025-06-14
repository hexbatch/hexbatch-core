<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Suspend extends Api\TypeApi
{
    const UUID = '24690363-fd1d-4344-a308-31809017b225';
    const TYPE_NAME = 'api_type_suspend';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ty\TypeSuspend::class
    ];

}

