<?php

namespace App\System\Resources\Types\Stock\System\Meta\MetaRegion;

use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\System\Meta\MetaRegion;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Us extends BaseType
{
    const UUID = 'ddbf00e9-faef-4e2f-a346-eaa46bae2489';
    const TYPE_NAME = 'region_usa';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [

    ];

    const PARENT_UUIDS = [
        MetaRegion::UUID
    ];

}

