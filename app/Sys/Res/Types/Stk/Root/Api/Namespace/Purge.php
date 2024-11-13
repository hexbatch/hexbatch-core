<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends Api\NamespaceApi
{
    const UUID = 'f3dd1916-1de2-4245-94c3-f04d9a7f0765';
    const TYPE_NAME = 'api_namespace_purge';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ns\NamespacePurge::class,
    ];

}

