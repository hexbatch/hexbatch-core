<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddRequirement extends BaseType
{
    const UUID = '1e785c55-92f2-423a-bf44-eccfda58f9dd';
    const TYPE_NAME = 'api_design_add_requirement';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\DesignRequirementAdd::class,
    ];

}

