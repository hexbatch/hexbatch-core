<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class ListListeners extends Api\DesignApi
{
    const UUID = '406430a7-ed87-4c8f-8f9f-9e9f33f4f37a';
    const TYPE_NAME = 'api_design_list_listeners';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Search::class,
    ];

}

