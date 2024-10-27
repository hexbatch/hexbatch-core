<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Operation;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Combine extends BaseType
{
    const UUID = '5e210a87-5c45-454d-bf71-b2fb5eaad97c';
    const TYPE_NAME = 'api_operation_combine';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\OperationApi::class,
        Act\Pragma\Search::class,
        Act\Op\Combine::class,
    ];

}

