<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListAdmins extends Api\NamespaceApi
{
    const UUID = 'db8eceab-7cf7-45c1-b16d-561ee32d3d58';
    const TYPE_NAME = 'api_namespace_list_admins';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

