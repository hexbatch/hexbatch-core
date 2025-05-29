<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateLocation extends Api\DesignApi
{
    const UUID = '508437a6-6307-4dba-b9f0-8ff14c91f583';
    const TYPE_NAME = 'api_design_location_test';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationCreate::class,
    ];

}

