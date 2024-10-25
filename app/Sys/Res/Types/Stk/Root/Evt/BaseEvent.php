<?php

namespace App\Sys\Res\Types\Stk\Root\Evt;

use App\Enums\Sys\TypeOfEvent;
use App\Models\Thing;
use App\Sys\Res\Atr\IAttribute;
use App\Sys\Res\Ele\IElement;
use App\Sys\Res\IEvent;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Servers\IServer;
use App\Sys\Res\Sets\ISet;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\IType;
use App\Sys\Res\Types\Stk\Root\Event;


class BaseEvent extends BaseType implements IEvent
{
    const UUID = 'a8334729-4371-4fcc-ba73-39cfef6e2529';

    const EVENT_NAME = TypeOfEvent::BASE_EVENT;

    public static function getName() :string { return static::EVENT_NAME->value; }

    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Event::class
    ];

    public function getRelatedActions(): array
    {
        return [];
    }

    public function PushEvent(IElement|IType|ISet|IServer|INamespace|IAttribute|null|\Illuminate\Support\Collection $source,
                              IElement|IType|ISet|IServer|INamespace|IAttribute|null                                $destination = null): Thing
    {
        //todo implement pushEvent
        return new Thing();
    }
}

