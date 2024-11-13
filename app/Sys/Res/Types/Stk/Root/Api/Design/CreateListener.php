<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateListener extends Api\DesignApi
{
    const UUID = 'd8b6670e-3ab8-4d7c-9a08-900fcc7d01d3';
    const TYPE_NAME = 'api_design_create_listener';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignListenerCreate::class,
    ];

}

