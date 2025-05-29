<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TransferOwner extends Api\NamespaceApi
{
    const UUID = 'ceec60f0-d947-405f-b33e-3a88c7fdeab0';
    const TYPE_NAME = 'api_namespace_transfer_owner';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ns\NamespaceTransferDo::class,
    ];

}

