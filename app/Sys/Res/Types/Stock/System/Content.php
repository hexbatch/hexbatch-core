<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stock\System;

use App\Sys\Res\Attributes\Stock\System\MetaData\Content\ContentData;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stock\SystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Content extends BaseType
{
    const UUID = 'c9f9671e-905f-4198-9049-ef0e1ad4d268';
    const TYPE_NAME = 'content';
    const NAMESPACE_UUID = ThisServerNamespace::UUID;

    const DESCRIPTION_ELEMENT_UUID = '';

    const ATTRIBUTE_UUIDS = [
        ContentData::UUID,
        ContentData\Subject::UUID,
        ContentData\Title::UUID,
        ContentData\Tags::UUID,
        ContentData\Blurb::UUID,
        ContentData\Body::UUID,
    ];

    const PARENT_UUIDS = [
        SystemType::UUID
    ];

}

