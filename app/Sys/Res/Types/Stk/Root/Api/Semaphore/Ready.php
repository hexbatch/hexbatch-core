<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Semaphore;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Ready extends BaseType
{
    const UUID = '5237670c-d893-469f-9e2e-8de5682cf632';
    const TYPE_NAME = 'api_semaphore_ready';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SemaphoreApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\SemaphoreReady::class,
    ];

}

