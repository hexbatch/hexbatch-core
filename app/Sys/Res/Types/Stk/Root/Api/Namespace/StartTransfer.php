<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class StartTransfer extends Api\NamespaceApi
{
    const UUID = 'f126f48f-9872-4b4a-924c-5d60a4f87c53';
    const TYPE_NAME = 'api_namespace_start_transfer';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ns\NamespaceTransferPre::class,
    ];

}

