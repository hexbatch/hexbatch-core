<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AskElement extends Api\ElsewhereApi
{
    const UUID = '939acf24-00f5-48b5-994a-4f7d3d990ffb';
    const TYPE_NAME = 'api_elsewhere_ask_element';





    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereAskElement::class,
    ];

}

