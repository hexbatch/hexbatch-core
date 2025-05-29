<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveParent extends Api\DesignApi
{
    const UUID = '93936e61-682b-43ed-a7ca-e6a9c610e242';
    const TYPE_NAME = 'api_design_remove_parent';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ds\DesignParentRemove::class,
    ];

}

