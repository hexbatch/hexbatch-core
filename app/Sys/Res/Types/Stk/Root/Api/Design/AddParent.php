<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddParent extends Api\DesignApi
{
    const UUID = 'abaa730a-bfe2-4437-bafe-493776ac1ca7';
    const TYPE_NAME = 'api_design_add_parent';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignParentAdd::class,
    ];

}

