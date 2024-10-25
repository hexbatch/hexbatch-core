<?php

namespace App\Sys\Res\Types\Stk\Root;



use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Action extends BaseType
{
    const UUID = '5453ef40-affb-4ea0-91dd-d3f998542288';
    const TYPE_NAME = 'action';

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Root::class
    ];

}

