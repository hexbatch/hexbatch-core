<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Set;

use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class RemoveElement extends Api\SetApi
{
    const UUID = 'ad756911-763d-4b76-932f-b7b632937857';
    const TYPE_NAME = 'api_set_remove_element';





    const PARENT_CLASSES = [
        Api\SetApi::class,
        Act\Cmd\Ele\Search::class,
        Act\Cmd\St\SetMemberRemove::class,
    ];

}

