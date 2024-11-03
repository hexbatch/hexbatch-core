<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AttributeLocation extends Api\DesignApi
{
    const UUID = 'db5971de-fe4e-498e-b2a5-12990cdb2b26';
    const TYPE_NAME = 'api_design_attribute_location';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributeLocation::class,
    ];

}

