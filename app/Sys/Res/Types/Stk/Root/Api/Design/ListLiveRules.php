<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class ListLiveRules extends Api\DesignApi
{
    const UUID = 'dfca4c86-030d-4345-9101-98ed01001535';
    const TYPE_NAME = 'api_design_list_live_rules';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Search::class
    ];

}

