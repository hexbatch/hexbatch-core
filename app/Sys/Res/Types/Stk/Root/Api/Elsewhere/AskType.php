<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AskType extends Api\ElsewhereApi
{
    const UUID = '47752a80-0eaf-44c3-bd9f-7a0316cc2aaa';
    const TYPE_NAME = 'api_elsewhere_ask_type';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereAskType::class,
    ];

}

