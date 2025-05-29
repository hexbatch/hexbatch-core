<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Pa\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class ListTimes extends Api\DesignApi
{
    const UUID = '5dc7b23e-c330-4cf6-8701-4e5db3c49946';
    const TYPE_NAME = 'api_design_list_times';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Search::class
    ];

}

