<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Location extends BaseType
{
    const UUID = '3f244752-6dd9-4aa9-bf44-4606d41e0630';
    const TYPE_NAME = 'api_design_location';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\DesignTypeLocation::class,
    ];

}

