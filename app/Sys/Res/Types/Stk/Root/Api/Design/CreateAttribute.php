<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class CreateAttribute extends BaseType
{
    const UUID = '745c1851-68af-4420-b6f9-037aa63bebc7';
    const TYPE_NAME = 'api_design_create_attribute';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeCreate::class,
    ];

}

