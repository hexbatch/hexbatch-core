<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveRequirement extends BaseType
{
    const UUID = '9626bab7-42eb-4e96-bb50-8696df857538';
    const TYPE_NAME = 'api_design_remove_requirement';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignRequirementRemove::class,
    ];

}

