<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;
use App\Sys\Res\Types\Stk\Root\Placeholder;


class Other extends BaseType
{
    const UUID = 'db4a2344-7f63-4fe6-bda4-8a3c519df638';
    const TYPE_NAME = 'other';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Placeholder\CurrentNamespace::class,
        BasePerNamespace::class
    ];

}

