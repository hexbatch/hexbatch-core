<?php

namespace App\Sys\Res\Types\Stock\System\Event;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Event;

/*
 * custom events inherit from Event , the scope event, and the NS type, they can be fired and listened to any in the ns members
 * custom event will be fired with the data given to it in the thing container, sent up by the children, it can be any data
 */

class CustomEvent extends BaseType
{
    const UUID = 'e5cd616a-72a1-45ce-a453-f4986c182259';
    const TYPE_NAME = 'custom_event';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Event::UUID
    ];

}

