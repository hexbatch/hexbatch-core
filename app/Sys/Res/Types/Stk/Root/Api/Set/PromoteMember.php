<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteMember extends BaseType
{
    const UUID = '4c8f7138-939b-4def-820f-1a55ab8bc433';
    const TYPE_NAME = 'api_set_promote_member';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\SetMemberPromote::class,
    ];

}

