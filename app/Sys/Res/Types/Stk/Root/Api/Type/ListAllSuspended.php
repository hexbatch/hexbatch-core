<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Type;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListAllSuspended extends Api\TypeApi
{
    const UUID = '38104747-18a6-4268-af36-aef420bad310';
    const TYPE_NAME = 'api_type_list_all_suspended';





    const PARENT_CLASSES = [
        Api\TypeApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

