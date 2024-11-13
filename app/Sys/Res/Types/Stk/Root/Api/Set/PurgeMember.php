<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PurgeMember extends Api\SetApi
{
    const UUID = 'ae109863-1181-469a-ac59-fe4ecbe6a67d';
    const TYPE_NAME = 'api_set_purge_member';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetMemberPurge::class,
    ];

}

