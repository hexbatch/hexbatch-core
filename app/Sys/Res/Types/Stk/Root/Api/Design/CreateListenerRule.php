<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateListenerRule extends Api\DesignApi
{
    const UUID = '9050b6c3-989c-4aa5-b252-ca1e2b53d579';
    const TYPE_NAME = 'api_design_create_listener_rule';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignRuleCreate::class,
    ];

}

