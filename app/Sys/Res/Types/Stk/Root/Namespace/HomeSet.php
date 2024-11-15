<?php

namespace App\Sys\Res\Types\Stk\Root\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Container;
use App\Sys\Res\Types\Stk\Root\NamespaceType;

/**
 * Home set always in default phase
 */
class HomeSet extends NamespaceType
{
    const UUID = '3bf5302c-7ded-468a-af01-a19dc135c806';
    const TYPE_NAME = 'home_set';





    const PARENT_CLASSES = [
        NamespaceType::class,
        Container::class
    ];

}

