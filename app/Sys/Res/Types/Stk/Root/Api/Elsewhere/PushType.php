<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class PushType extends Api\ElsewhereApi
{
    const UUID = '57a1d6dc-de67-4f93-bfca-5db34abe9177';
    const TYPE_NAME = 'api_elsewhere_push_type';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewherePushType::class,
    ];

}

