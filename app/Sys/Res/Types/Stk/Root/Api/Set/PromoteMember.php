<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteMember extends Api\SetApi
{
    const UUID = '4c8f7138-939b-4def-820f-1a55ab8bc433';
    const TYPE_NAME = 'api_set_promote_member';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetMemberPromote::class,
    ];

}

