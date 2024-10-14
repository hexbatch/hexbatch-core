<?php

namespace App\Sys\Res\Types\Stk\System\Evt;

use App\Models\Thing;
use App\Sys\Res\Atr\IAttribute;
use App\Sys\Res\Ele\IElement;
use App\Sys\Res\IEvent;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Servers\IServer;
use App\Sys\Res\Sets\ISet;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\IType;
use App\Sys\Res\Types\Stk\System\Event;


class BaseEvent extends BaseType implements IEvent
{
    const UUID = 'c70f2c0b-cd6d-4a20-8f46-05a72ba6a68f';
    const TYPE_NAME = 'base_event';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event::UUID
    ];

    public function getRelatedActions(): array
    {
        return [];
    }

    public function PushEvent(IElement|IType|ISet|IServer|INamespace|IAttribute|null $source,
                              IElement|IType|ISet|IServer|INamespace|IAttribute|null $destination = null): Thing
    {
        //todo implement pushEvent
        return new Thing();
    }
}

