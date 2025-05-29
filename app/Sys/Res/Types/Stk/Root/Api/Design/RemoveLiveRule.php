<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveLiveRule extends Api\DesignApi
{
    const UUID = '3a57c06a-a9a1-4ec6-9762-c0d65a97b58d';
    const TYPE_NAME = 'api_design_remove_live_rule';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLiveRuleRemove::class,
    ];

}

