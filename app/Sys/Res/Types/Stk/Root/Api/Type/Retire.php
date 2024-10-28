<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Retire extends BaseType
{
    const UUID = '9b7f562b-5de8-4569-89ea-98747e542e01';
    const TYPE_NAME = 'api_type_retire';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\Ty\TypeRetire::class
    ];

}

