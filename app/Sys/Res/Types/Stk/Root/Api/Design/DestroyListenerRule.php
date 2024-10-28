<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroyListenerRule extends BaseType
{
    const UUID = 'd20a2fae-be28-497b-ac47-61adae1e3ce4';
    const TYPE_NAME = 'api_design_destroy_listener_rule';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignRuleDestroy::class,
    ];

}

