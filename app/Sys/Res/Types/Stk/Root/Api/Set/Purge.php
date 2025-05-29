<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends Api\SetApi
{
    const UUID = '48935711-8f68-457d-a746-d20deb8505bf';
    const TYPE_NAME = 'api_set_purge';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\St\SetPurge::class,
    ];

}

