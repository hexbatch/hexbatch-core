<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PurgeMember extends Api\NamespaceApi
{
    const UUID = '66f92d1c-8555-4630-9f7f-d95d3b2b2b8d';
    const TYPE_NAME = 'api_namespace_purge_member';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ns\NamespaceMemberPurge::class,
    ];

}

