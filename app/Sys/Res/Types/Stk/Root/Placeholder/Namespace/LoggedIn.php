<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Namespace\BasePerNamespace;
use App\Sys\Res\Types\Stk\Root\Placeholder;


class LoggedIn extends BaseType
{
    const UUID = '8e69a890-ac55-451a-971e-ca9b0f9357e2';
    const TYPE_NAME = 'logged_in';





    const PARENT_CLASSES = [
        Placeholder\CurrentNamespace::class,
        BasePerNamespace::class
    ];

}

