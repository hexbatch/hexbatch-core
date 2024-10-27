<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Create extends BaseType
{
    const UUID = 'ba68ba5e-37c5-45bd-9ca5-eadf0c798ef4';
    const TYPE_NAME = 'api_namespace_create';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\NamespaceCreate::class,
    ];

}

