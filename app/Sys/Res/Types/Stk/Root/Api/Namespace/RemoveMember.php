<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveMember extends BaseType
{
    const UUID = '310b7928-d5a6-4fcd-9ab7-3ca85d932408';
    const TYPE_NAME = 'api_namespace_remove_member';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\NamespaceMemberRemove::class,
    ];

}

