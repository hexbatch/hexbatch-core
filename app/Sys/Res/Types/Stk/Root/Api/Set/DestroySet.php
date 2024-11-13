<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroySet extends Api\SetApi
{
    const UUID = '41b929cf-e23e-4ef3-a0b4-d7120a8ad578';
    const TYPE_NAME = 'api_set_destroy';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetDestroy::class,
    ];

}

