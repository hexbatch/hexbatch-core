<?php

namespace App\Sys\Res\Types\Stk\Root\Act;

use App\Models\Thing;
use App\Sys\Res\IAction;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Action;


class BaseAction extends BaseType implements IAction
{
    const UUID = 'ebdcbddd-c746-44dc-84b0-cf1f8f174b2b';
    const TYPE_NAME = 'base_action';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Action::UUID
    ];

    const EVENT_UUIDS = [];

    public function doAction(Thing $thing): IAction
    {
        return $this;
    }

    public function getRelatedEvents(): array
    {
        return [];
    }
}

