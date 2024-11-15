<?php

namespace App\Sys\Res;


use App\Sys\Res\Types\Stk\Root\Evt\BaseEvent;

interface IAction
{


    /** @return BaseEvent[]|string[] */
    public static function getRelatedEvents(): array;

}
