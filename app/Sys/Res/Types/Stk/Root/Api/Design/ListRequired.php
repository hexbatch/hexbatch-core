<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class ListRequired extends Api\DesignApi
{
    const UUID = 'cecd1cdc-97ea-4818-a4db-6a1576deda0a';
    const TYPE_NAME = 'api_design_list_required';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Search::class
    ];

}

