<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class DestroyedElement extends Api\ElsewhereApi
{
    const UUID = '32bbfc5d-b15a-4219-b777-ae226a7210bf';
    const TYPE_NAME = 'api_elsewhere_destroyed_event';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereDestroyedElement::class,
    ];

}

