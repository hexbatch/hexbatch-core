<?php

namespace App\Sys\Res\Ele\Stk\Placeholders;


use App\Sys\Res\Ele\BaseElement;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\Stk\Root\Placeholder\CurrentSet;

class CurrentSetDefiner extends BaseElement
{
    const UUID = '8413b20c-1826-4268-ab1d-bde71f4c9af0';
    const TYPE_CLASS = CurrentSet::class;
    const NAMESPACE_CLASS = ThisServerNamespace::class;

}


