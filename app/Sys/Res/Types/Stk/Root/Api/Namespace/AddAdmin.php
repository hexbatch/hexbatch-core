<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class AddAdmin extends BaseType
{
    const UUID = 'ac34e519-cd58-4281-9799-8cffc7e0137b';
    const TYPE_NAME = 'api_namespace_add_admin';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Pragma\Search::class,
        Act\Cmd\NamespaceAdminAdd::class,
    ];

}

