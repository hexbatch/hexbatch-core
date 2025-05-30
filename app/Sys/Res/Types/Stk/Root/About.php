<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\About\AboutThis;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Atr\Stk\About\Abt;
use App\Sys\Res\Types\Stk\Root;

class About extends BaseType
{
    const UUID = '80603b6a-0720-4751-90e4-dea4cd88c363';
    const TYPE_NAME = 'about';



    const ATTRIBUTE_CLASSES = [
        AboutThis::class,
        Abt\Description::class,
        Abt\Privacy::class,
        Abt\Readme::class,
        Abt\Terms::class,
        Abt\Title::class,
        Abt\AboutUs::class,
    ];

    const PARENT_CLASSES = [
        Root::class
    ];



}

