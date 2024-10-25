<?php

namespace App\Sys\Res\Types\Stk\Root;



use App\Sys\Res\Atr\Stk\Event\Scope\ChainScope;
use App\Sys\Res\Atr\Stk\Event\Scope\ElementScope;
use App\Sys\Res\Atr\Stk\Event\Scope\ServerScope;
use App\Sys\Res\Atr\Stk\Event\Scope\SetScope;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Event extends BaseType
{
    const UUID = '25df7e1f-7825-4528-b331-9e93d613a962';
    const TYPE_NAME = 'events';



    const ATTRIBUTE_CLASSES = [
        ElementScope::class,
        SetScope::class,
        ChainScope::class,
        ServerScope::class,
    ];

    const PARENT_CLASSES = [
        Root::class
    ];

}

