<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class GiveType extends BaseType
{
    const UUID = '60a56e19-d29c-45b9-ad2a-7c138ad3ba97';
    const TYPE_NAME = 'api_elsewhere_give_type';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ew\ElsewhereGiveType::class,
    ];

}

