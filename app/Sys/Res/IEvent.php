<?php

namespace App\Sys\Res;

interface IEvent
{
    const EVENT_NAME = '';


    /** @return IAction[] */
    public function getRelatedActions(): array;

}
