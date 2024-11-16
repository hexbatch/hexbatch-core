<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PromoteSet extends Api\SetApi
{
    const UUID = '81ca3eef-fc4c-4c9a-9f95-7d2c2bf00ec4';
    const TYPE_NAME = 'api_element_promote_set';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\SetPromote::class,
    ];

}

