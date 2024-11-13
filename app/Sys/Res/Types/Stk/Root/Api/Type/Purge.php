<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends Api\TypeApi
{
    const UUID = 'e7beaf16-f329-4cc0-a6ab-1b18c76d8aac';
    const TYPE_NAME = 'api_type_purge';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ty\TypePurge::class
    ];

}

