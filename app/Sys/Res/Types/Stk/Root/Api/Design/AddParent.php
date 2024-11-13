<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddParent extends Api\DesignApi
{
    const UUID = 'abaa730a-bfe2-4437-bafe-493776ac1ca7';
    const TYPE_NAME = 'api_design_add_parent';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ds\DesignParentAdd::class,
    ];

}

