<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Promotion extends Api\DesignApi
{
    const UUID = 'd7f78fde-0a6b-4e02-85f5-7d9f19f09747';
    const TYPE_NAME = 'api_design_promotion';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignPromotion::class,
    ];

}

