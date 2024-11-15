<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class SuspendedType extends Api\ElsewhereApi
{
    const UUID = '4b4dfd61-0e73-4eda-9af9-4322d1855776';
    const TYPE_NAME = 'api_elsewhere_suspended_type';


    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereSuspendedType::class,
    ];

}

