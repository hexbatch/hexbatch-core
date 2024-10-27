<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteMember extends BaseType
{
    const UUID = 'e82a0c23-ae51-4abc-bf02-3d4a00268dbe';
    const TYPE_NAME = 'api_namespace_promote_member';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\NamespaceMemberPromote::class,
    ];

}

