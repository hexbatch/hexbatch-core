<?php

namespace App\Sys\Res;


interface IAction
{
    const ACTION_NAME = '';

    const EVENT_CLASSES = [];

    /** @return IEvent[] */
    public function getRelatedEvents(): array;
}
