<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveHandle extends Api\NamespaceApi
{
    const UUID = 'e2348f4c-08f3-4865-ad22-9b3cf9c97a72';
    const TYPE_NAME = 'api_namespace_remove_handle';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceHandleRemove::class,
    ];

}

