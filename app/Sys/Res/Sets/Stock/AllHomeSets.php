<?php

namespace App\Sys\Res\Sets\Stock;


use App\Sys\Res\Elements\Stock\Homesets;
use App\Sys\Res\Elements\Stock\SystemNSElements\SystemHomeSetElement;
use App\Sys\Res\Sets\BaseSet;

class AllHomeSets extends BaseSet
{
    const UUID = 'b2817957-c92e-44ff-9ccc-6b6d0877e5f3';
    const ELEMENT_UUID = Homesets::UUID;

    const CONTAINING_ELEMENT_UUIDS = [
        SystemHomeSetElement::UUID
    ];

}


