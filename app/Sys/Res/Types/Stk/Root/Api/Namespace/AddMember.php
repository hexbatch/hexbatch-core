<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddMember extends Api\NamespaceApi
{
    const UUID = '265a64ec-f02c-44f1-9474-80c50e4f737b';
    const TYPE_NAME = 'api_namespace_add_member';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ns\NamespaceMemberAdd::class,
    ];

}

