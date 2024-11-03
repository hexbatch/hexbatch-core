<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroyListener extends Api\DesignApi
{
    const UUID = '0d3019f1-34f2-4637-9016-8123696be0ea';
    const TYPE_NAME = 'api_design_destroy_listener';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignListenerDestroy::class,
    ];

}

