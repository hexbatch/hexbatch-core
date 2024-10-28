<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Elsewhere;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Register extends BaseType
{
    const UUID = '0a3e3036-f9f2-4710-baf5-5825fc833770';
    const TYPE_NAME = 'api_elsewhere_register';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\ElsewhereApi::class,
        Act\Cmd\Ew\ElsewhereDoRegistration::class,
    ];

}

