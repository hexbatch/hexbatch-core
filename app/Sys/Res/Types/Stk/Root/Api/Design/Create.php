<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Create extends Api\DesignApi
{
    const UUID = 'eb4805a7-3f25-4aae-a42b-4308edc66185';
    const TYPE_NAME = 'api_design_create';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignCreate::class,
    ];

}

