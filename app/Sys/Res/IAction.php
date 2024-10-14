<?php

namespace App\Sys\Res;

use App\Models\Thing;

interface IAction
{
    const ACTION_NAME = '';
    public function doAction(Thing $thing): IAction;

    /** @return IEvent[] */
    public function getRelatedEvents(): array;
}
