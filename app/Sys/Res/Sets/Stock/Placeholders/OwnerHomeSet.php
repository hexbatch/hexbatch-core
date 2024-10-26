<?php

namespace App\Sys\Res\Sets\Stock\Placeholders;


use App\Sys\Res\Ele\Stk\Holder\CurrentSetDefinerElement;
use App\Sys\Res\Ele\Stk\Holder\Owner\OwnerHomeSetElement;
use App\Sys\Res\Sets\BaseSet;

class OwnerHomeSet extends BaseSet
{
    const UUID = 'fadd797c-b562-400e-ba43-b746c514df78';
    const ELEMENT_CLASS = OwnerHomeSetElement::class;

    const CONTAINING_ELEMENT_CLASSES = [
        CurrentSetDefinerElement::class
    ];

}


