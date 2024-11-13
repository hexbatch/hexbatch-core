<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteAdmin extends Api\NamespaceApi
{
    const UUID = '81a2092b-2bb0-4be7-a9c4-aacd608f6ea3';
    const TYPE_NAME = 'api_namespace_promote_admin';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ns\NamespaceAdminPromote::class,
    ];

}

