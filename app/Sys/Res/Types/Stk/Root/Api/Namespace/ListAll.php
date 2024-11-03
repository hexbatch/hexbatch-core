<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListAll extends Api\NamespaceApi
{
    const UUID = '52fbdcdd-1d64-4ea5-90c4-e68d03df825c';
    const TYPE_NAME = 'api_namespace_list_all';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

