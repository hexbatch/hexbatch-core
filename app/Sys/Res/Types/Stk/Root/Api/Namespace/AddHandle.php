<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddHandle extends Api\NamespaceApi
{
    const UUID = '0749c44d-f474-4b1d-8cea-40ceb74809fc';
    const TYPE_NAME = 'api_namespace_add_handle';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceHandleAdd::class,
    ];

}

