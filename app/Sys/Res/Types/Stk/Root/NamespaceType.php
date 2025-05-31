<?php


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\MetaData\NamespaceType\NamespaceData;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;


class NamespaceType extends BaseType
{
    const UUID = 'f6952b0a-cf14-46c6-9695-36f489fbc732';
    const TYPE_NAME = 'namespace';

    const bool IS_PUBLIC_DOMAIN = false;

    const ATTRIBUTE_CLASSES = [
        NamespaceData::class
    ];

    const PARENT_CLASSES = [
        Root::class
    ];


}



