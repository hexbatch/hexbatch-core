<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Destroy extends BaseType
{
    const UUID = '29699a32-a22d-44b8-9525-91d27f9fc33b';
    const TYPE_NAME = 'api_namespace_destroy';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceDestroy::class,
    ];

}

