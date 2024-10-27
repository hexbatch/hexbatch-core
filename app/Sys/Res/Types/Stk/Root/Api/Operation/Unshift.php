<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Operation;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Unshift extends BaseType
{
    const UUID = 'bd42c53f-c4ac-406e-9412-dd835f93e97b';
    const TYPE_NAME = 'api_operation_unshift';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\OperationApi::class,
        Act\Pragma\Search::class,
        Act\Op\Unshift::class,
    ];

}

