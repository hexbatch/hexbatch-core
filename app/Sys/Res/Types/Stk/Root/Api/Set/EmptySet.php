<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class EmptySet extends BaseType
{
    const UUID = '7b4346fb-16e9-4561-b57b-2778934e8ca1';
    const TYPE_NAME = 'api_set_empty';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetEmpty::class,
    ];

}

