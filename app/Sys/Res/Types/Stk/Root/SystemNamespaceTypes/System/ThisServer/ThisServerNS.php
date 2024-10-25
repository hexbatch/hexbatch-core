<?php

namespace App\Sys\Res\Types\Stk\Root\SystemNamespaceTypes\System\ThisServer;

use App\Sys\Res\Ele\Stk\SystemNSElements\SystemDescriptionElement;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;


class ThisServerNS extends BaseType
{
    const UUID = '264b037f-2383-46e6-a157-1208faf2985e';
    const TYPE_NAME = 'system_namespace';


    const DESCRIPTION_ELEMENT_UUID = SystemDescriptionElement::class;
    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        BasePerNamespace::class,
    ];

}

