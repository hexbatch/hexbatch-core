<?php

namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/*
 * Type must have this in up-type for exported elements to be put on the exports table
 * if missing can still be exported but not tracked
 */
class TrackingExported extends BaseType
{
    const UUID = 'b66fa0bf-1c30-4a12-85d8-ea2e2f233157';
    const TYPE_NAME = 'tracking_exported';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}

