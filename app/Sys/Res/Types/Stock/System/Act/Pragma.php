<?php

namespace App\Sys\Res\Types\Stock\System\Act;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\System\Action;


class Pragma extends BaseType
{
    const UUID = '0990d423-b26d-4191-9cee-3d04464448bc';
    const TYPE_NAME = 'pragma';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Action::UUID
    ];

}

