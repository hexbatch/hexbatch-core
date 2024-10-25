<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;


class HomeSetType extends BaseType
{
    const UUID = '77d5ba3d-4b0b-4d33-91ba-d5f909a1ebbf';
    const TYPE_NAME = 'owner_home_set';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Owner::class,
        \App\Sys\Res\Types\Stk\Root\Namespace\HomeSet::class
    ];

}

