<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;


class PublicType extends BaseType
{
    const UUID = 'a960fb30-7351-4362-8a21-390ae3c85dcd';
    const TYPE_NAME = 'logged_in_public';





    const PARENT_CLASSES = [
        LoggedIn::class,
        \App\Sys\Res\Types\Stk\Root\Namespace\PublicType::class
    ];

}

