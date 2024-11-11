<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class ListParents extends Api\DesignApi
{
    const UUID = 'bedf6fef-3e7a-4c05-966d-669cb879fd2a';
    const TYPE_NAME = 'api_design_list_parents';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Search::class
    ];

}

