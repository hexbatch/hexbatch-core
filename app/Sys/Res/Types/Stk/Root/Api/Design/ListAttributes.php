<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\Search;
use App\Sys\Res\Types\Stk\Root\Api;


class ListAttributes extends Api\DesignApi
{
    const UUID = '293ec496-e455-4dbe-8058-c6b528370268';
    const TYPE_NAME = 'api_design_list_attributes';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Search::class,
    ];

}

