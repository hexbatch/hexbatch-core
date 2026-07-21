<?php

namespace App\Sys\Res\Types\Stk\Root\NsSysTypes;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\NamespaceBase;

class ThisNsType extends BaseType
{
    const TYPE_NAME = 'system_namespace';





    const PARENT_CLASSES = [
        NamespaceBase::class,
    ];


}

