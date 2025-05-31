<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\Placeholder\PlaceholderAttribute;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;


class Placeholder extends BaseType
{
    const UUID = '4d1910aa-c16f-4fca-b8c0-e84094d2d76a';
    const TYPE_NAME = 'placeholder';

    const bool IS_PUBLIC_DOMAIN = false;

    const ATTRIBUTE_CLASSES = [
        PlaceholderAttribute::class
    ];

    const PARENT_CLASSES = [
        Root::class
    ];



}

