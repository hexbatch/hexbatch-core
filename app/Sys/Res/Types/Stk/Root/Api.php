<?php

namespace App\Sys\Res\Types\Stk\Root;



use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Api extends BaseType
{
    const UUID = 'd314149a-0f51-4b1e-b954-590a890e7c44';
    const TYPE_NAME = 'api';




    const PARENT_CLASSES = [
        Root::class
    ];

}

