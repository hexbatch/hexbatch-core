<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class ListLocations extends Api\DesignApi
{
    const UUID = 'db5971de-fe4e-498e-b2a5-12990cdb2b26';
    const TYPE_NAME = 'api_design_list_locations';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Search::class
    ];

}

