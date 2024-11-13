<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Time extends Api\DesignApi
{
    const UUID = 'b3b52738-f425-4083-9648-e777837696b7';
    const TYPE_NAME = 'api_design_time';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTime::class,
    ];

}

