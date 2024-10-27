<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class ShowAdmins extends BaseType
{
    const UUID = 'db8eceab-7cf7-45c1-b16d-561ee32d3d58';
    const TYPE_NAME = 'api_namespace_admins';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Pragma\Search::class
    ];

}

