<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AttributePromotion extends Api\DesignApi
{
    const UUID = '79243b59-bd99-4768-b539-15f90017537f';
    const TYPE_NAME = 'api_design_attribute_promotion';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignAttributePromotion::class,
    ];

}

