<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteOwner extends Api\TypeApi
{
    const UUID = '1b329981-9c9f-4645-ab71-1352969aff0d';
    const TYPE_NAME = 'api_type_promote_owner';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ty\TypeOwnerPromote::class
    ];

}

