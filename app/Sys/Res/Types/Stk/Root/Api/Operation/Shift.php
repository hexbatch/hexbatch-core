<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Operation;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Shift extends BaseType
{
    const UUID = '2c8d3bbc-f930-49c8-bf1d-039e5a5ef28f';
    const TYPE_NAME = 'api_operation_shift';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\OperationApi::class,
        Act\Pragma\Search::class,
        Act\Op\Shift::class,
    ];

}

