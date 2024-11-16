<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListChildren extends Api\SetApi
{
    const UUID = '9897cd72-0322-49c8-8f80-5367ab5d86cf';
    const TYPE_NAME = 'api_set_list_children';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Pa\Search::class
    ];

}

