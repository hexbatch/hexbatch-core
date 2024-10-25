<?php

namespace App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Placeholder\Namespace\Owner;


class PublicType extends BaseType
{
    const UUID = '4b9cf1b3-1568-4d2a-9be0-044d725783a4';
    const TYPE_NAME = 'owner_public';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Owner::class,
        \App\Sys\Res\Types\Stk\Root\Namespace\PublicType::class
    ];

}

