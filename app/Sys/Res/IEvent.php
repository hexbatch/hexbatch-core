<?php

namespace App\Sys\Res;

use App\Models\Thing;
use App\Sys\Res\Atr\IAttribute;
use App\Sys\Res\Ele\IElement;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Servers\IServer;
use App\Sys\Res\Sets\ISet;
use App\Sys\Res\Types\IType;

interface IEvent
{
    const EVENT_NAME = '';


    /** @return IAction[] */
    public function getRelatedActions(): array;

    public function PushEvent(
        ISet|IElement|IType|INamespace|IAttribute|IServer|null $source,
        ISet|IElement|IType|INamespace|IAttribute|IServer|null $destination = null
    )
    : Thing;
}
