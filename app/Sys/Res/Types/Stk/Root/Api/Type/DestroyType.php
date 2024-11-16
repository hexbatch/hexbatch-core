<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroyType extends Api\TypeApi
{
    const UUID = '8eee9671-d894-4eac-8cc2-dc0d26256a4d';
    const TYPE_NAME = 'api_type_destroy';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Pa\Search::class,
        Act\Cmd\Ty\TypeDestroy::class
    ];

}

