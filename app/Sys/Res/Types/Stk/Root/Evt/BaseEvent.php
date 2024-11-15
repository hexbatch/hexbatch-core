<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;

use App\Enums\Sys\TypeOfEvent;
use App\Sys\Res\IEvent;
use App\Sys\Res\Types\Stk\Root\Event;


class BaseEvent extends Event implements IEvent
{
    const UUID = 'a8334729-4371-4fcc-ba73-39cfef6e2529';

    const EVENT_NAME = TypeOfEvent::BASE_EVENT;

    public static function getClassName() :string { return static::EVENT_NAME->value; }
    public static function getEventName() :string { return static::EVENT_NAME->value; }



    const PARENT_CLASSES = [
        Event::class
    ];


}

