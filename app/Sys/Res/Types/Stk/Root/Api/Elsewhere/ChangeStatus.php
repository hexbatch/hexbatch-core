<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ChangeStatus extends BaseType
{
    const UUID = '6fcb3b35-e05f-4c9e-b0d7-554f1031ca0d';
    const TYPE_NAME = 'api_elsewhere_change_status';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereChangeStatus::class,
    ];

}

