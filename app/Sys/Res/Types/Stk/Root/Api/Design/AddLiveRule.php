<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddLiveRule extends BaseType
{
    const UUID = 'bfda8c68-52ae-4e44-8616-0cfca871e338';
    const TYPE_NAME = 'api_design_add_live_rule';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLiveRuleAdd::class,
    ];

}

