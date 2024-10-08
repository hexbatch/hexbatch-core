<?php
//  this is some news or message


namespace App\System\Resources\Types\Stock\System;

use App\System\Resources\Attributes\Stock\System\MetaData\Content\ContentData;
use App\System\Resources\Attributes\Stock\System\MetaData\Display\DisplayData;
use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseType;
use App\System\Resources\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Content extends BaseType
{
    const UUID = 'c9f9671e-905f-4198-9049-ef0e1ad4d268';
    const TYPE_NAME = 'content';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        ContentData::UUID
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}
/*
 * tags
 * title
 * subject
 * blurb
 * body
 */
