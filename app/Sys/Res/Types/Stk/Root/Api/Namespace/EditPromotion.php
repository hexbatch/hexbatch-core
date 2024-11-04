<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class EditPromotion extends Api\NamespaceApi
{
    const UUID = '3fafd8d8-d8da-4eba-87ac-b16c45366754';
    const TYPE_NAME = 'api_namespace_edit_promotion';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\NamespaceEditPromotion::class,
    ];

}

