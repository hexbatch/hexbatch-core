<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PurgeAdmin extends BaseType
{
    const UUID = 'de5b69ce-c9cc-475b-ac5b-c61a80d70f39';
    const TYPE_NAME = 'api_namespace_purge_admin';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ns\NamespaceAdminPurge::class,
    ];

}

