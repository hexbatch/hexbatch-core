<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddHandleAttribute extends BaseType
{
    const UUID = '11893030-3f8e-49b7-97cb-784f8887f092';
    const TYPE_NAME = 'api_type_attribute_add_handle';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\AttributeHandleAdd::class
    ];

}

