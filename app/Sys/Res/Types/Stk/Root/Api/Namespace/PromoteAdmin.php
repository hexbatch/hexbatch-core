<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteAdmin extends BaseType
{
    const UUID = '81a2092b-2bb0-4be7-a9c4-aacd608f6ea3';
    const TYPE_NAME = 'api_namespace_promote_admin';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ns\NamespaceAdminPromote::class,
    ];

}

