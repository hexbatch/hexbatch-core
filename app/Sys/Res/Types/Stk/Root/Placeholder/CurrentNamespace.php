<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\NamespaceType;
use App\Sys\Res\Types\Stk\Root\Placeholder;


class CurrentNamespace extends BaseType
{
    const UUID = '86998a06-d158-402c-8250-8bc5257710f6';
    const TYPE_NAME = 'current_namespace';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Placeholder::class,
        NamespaceType::class,
    ];

}

