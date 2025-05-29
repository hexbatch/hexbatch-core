<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AskSet extends Api\ElsewhereApi
{
    const UUID = 'ca0e2b83-b6e5-4751-bedb-98c35c1e5b7f';
    const TYPE_NAME = 'api_elsewhere_ask_set';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereAskSet::class,
    ];

}

