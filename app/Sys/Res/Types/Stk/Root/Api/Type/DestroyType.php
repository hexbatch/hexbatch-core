<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroyType extends BaseType
{
    const UUID = '8eee9671-d894-4eac-8cc2-dc0d26256a4d';
    const TYPE_NAME = 'api_type_destroy';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ty\TypeDestroy::class
    ];

}

