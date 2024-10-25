<?php

namespace App\Sys\Res\Sets\Stock;


use App\Sys\Res\Ele\Stk\SystemNSElements\SystemDescriptionElement;
use App\Sys\Res\Ele\Stk\SystemNSElements\SystemHomeSetElement;
use App\Sys\Res\Sets\BaseSet;

class SystemHomeSet extends BaseSet
{
    const UUID = '4c16b579-3574-49c1-baef-81e3985620ad';
    const ELEMENT_CLASS = SystemHomeSetElement::class;

    const CONTAINING_ELEMENT_CLASSES = [
        SystemDescriptionElement::class
    ];

}


