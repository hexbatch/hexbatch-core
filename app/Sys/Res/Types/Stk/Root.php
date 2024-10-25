<?php

namespace App\Sys\Res\Types\Stk;



use App\Sys\Res\Atr\Stk\Event\Scope;
use App\Sys\Res\Atr\Stk\System\Expiration;
use App\Sys\Res\Atr\Stk\System\OutsideUrl;
use App\Sys\Res\Types\BaseType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Root extends BaseType
{
    const UUID = '79a56b04-c36e-430f-bad4-5f53fb29ad4e';
    const TYPE_NAME = 'system';



    const ATTRIBUTE_CLASSES = [
        Expiration::class,
        OutsideUrl::class,
        Scope::class
    ];

    const PARENT_CLASSES = [];

}

