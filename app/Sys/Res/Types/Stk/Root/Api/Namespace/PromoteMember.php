<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteMember extends Api\NamespaceApi
{
    const UUID = 'e82a0c23-ae51-4abc-bf02-3d4a00268dbe';
    const TYPE_NAME = 'api_namespace_promote_member';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ns\NamespaceMemberPromote::class,
    ];

}

