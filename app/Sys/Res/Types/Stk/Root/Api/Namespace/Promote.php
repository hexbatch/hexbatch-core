<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class Promote extends Api\NamespaceApi
{
    const UUID = '44dc6436-b893-4af2-a89c-883cb4c339ac';
    const TYPE_NAME = 'api_namespace_promote';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespacePromote::class,
    ];

}

