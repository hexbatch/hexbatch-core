<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroyAttribute extends BaseType
{
    const UUID = '9ab860e3-fff0-4fdd-b18c-f9b33365692f';
    const TYPE_NAME = 'api_design_destroy_attribute';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeDestroy::class,
    ];

}

