<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Suspend extends BaseType
{
    const UUID = '24690363-fd1d-4344-a308-31809017b225';
    const TYPE_NAME = 'api_type_suspend';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ty\TypeSuspend::class
    ];

}

