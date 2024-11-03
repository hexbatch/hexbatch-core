<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListMembers extends Api\SetApi
{
    const UUID = 'cd570e6a-8a1f-4d96-9cfa-76708d501346';
    const TYPE_NAME = 'api_set_list_members';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

