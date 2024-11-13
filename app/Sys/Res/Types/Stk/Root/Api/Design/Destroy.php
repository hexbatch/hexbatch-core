<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Destroy extends Api\DesignApi
{
    const UUID = '74ff2b6e-4b93-4db1-b8fe-c3eb672cc16b';
    const TYPE_NAME = 'api_design_destroy';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignDestroy::class,
    ];

}

