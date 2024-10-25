<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\LoggedIn;


class PrivateType extends BaseType
{
    const UUID = '54085cb6-1143-4793-934d-ffc844e00a4a';
    const TYPE_NAME = 'logged_in_private';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        LoggedIn::class,
        \App\Sys\Res\Types\Stk\Root\Namespace\PrivateType::class
    ];

}

