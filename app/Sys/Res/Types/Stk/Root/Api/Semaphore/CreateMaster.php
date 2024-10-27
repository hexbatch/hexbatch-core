<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Semaphore;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateMaster extends BaseType
{
    const UUID = '21aeb6ed-27ce-4110-8258-ea8300570a7c';
    const TYPE_NAME = 'api_semaphore_create_master';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SemaphoreApi::class,
        Act\Cmd\SemaphoreMasterCreate::class,
    ];

}

