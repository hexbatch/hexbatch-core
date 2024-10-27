<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveAdmin extends BaseType
{
    const UUID = '1ad37810-9cf6-4d9e-a2ca-ea0488b2371a';
    const TYPE_NAME = 'api_namespace_remove_admin';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\NamespaceAdminRemove::class,
    ];

}

