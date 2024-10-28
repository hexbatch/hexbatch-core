<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class GiveSet extends BaseType
{
    const UUID = '246996d4-90cb-4fa5-aeff-9773d866292c';
    const TYPE_NAME = 'api_elsewhere_give_set';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ew\ElsewhereGiveSet::class,
    ];

}

