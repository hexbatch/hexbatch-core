<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Namespace;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class StartUserDeletion extends Api\NamespaceApi
{
    const UUID = '2bf19367-618b-4ef4-8b56-00b2e6717f7d';
    const TYPE_NAME = 'api_namespace_start_user_deletion';





    const PARENT_CLASSES = [
        Api\NamespaceApi::class,
        Act\Cmd\Ns\PrepareUserDeletion::class,
    ];

}

