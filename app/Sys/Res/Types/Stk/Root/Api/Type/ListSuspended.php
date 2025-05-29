<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListSuspended extends Api\TypeApi
{
    const UUID = 'd7ca746f-c541-4f6d-b7a8-165434499922';
    const TYPE_NAME = 'api_type_list_suspended';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Pa\Search::class
    ];

}

