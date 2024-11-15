<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ShareElement extends Api\ElsewhereApi
{
    const UUID = 'b3e0652b-dcb4-4508-9e5d-2c448f2fd92a';
    const TYPE_NAME = 'api_elsewhere_share_element';


    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereSharingElement::class,
    ];

}

