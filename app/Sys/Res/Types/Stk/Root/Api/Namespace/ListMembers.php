<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ListMembers extends Api\NamespaceApi
{
    const UUID = '28300dbe-984c-4084-ab4d-f7c7dfa529d9';
    const TYPE_NAME = 'api_namespace_list_members';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ele\Search::class
    ];

}

