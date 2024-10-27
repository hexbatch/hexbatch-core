<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class GiveCredentials extends BaseType
{
    const UUID = '4f4d4bc7-c51c-464c-8005-215865837be4';
    const TYPE_NAME = 'api_elsewhere_give_credentials';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\ElsewhereGiveCredentials::class,
    ];

}

