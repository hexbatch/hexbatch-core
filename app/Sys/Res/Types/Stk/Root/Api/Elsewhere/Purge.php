<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Purge extends Api\ElsewhereApi
{
    const UUID = '960652b5-895b-4fc2-a2a4-b7ad54c6e562';
    const TYPE_NAME = 'api_elsewhere_purge';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewherePurge::class,
    ];

}

