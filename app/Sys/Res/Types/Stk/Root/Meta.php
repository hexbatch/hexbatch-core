<?php


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\MetaData\Metadata;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;


class Meta extends BaseType
{
    const UUID = 'f675592e-dacd-471e-a679-971a09c00b4d';
    const TYPE_NAME = 'meta';



    const ATTRIBUTE_CLASSES = [
        Metadata::class,
    ];

    const PARENT_CLASSES = [
        Root::class
    ];

}
