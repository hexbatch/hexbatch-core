<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\MetaData\Content\ContentData;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

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
        \App\Sys\Res\Atr\Stk\MetaData\Content\ContentData\Subject::UUID,
        ContentData\Title::UUID,
        \App\Sys\Res\Atr\Stk\MetaData\Content\ContentData\Tags::UUID,
        ContentData\Blurb::UUID,
        \App\Sys\Res\Atr\Stk\MetaData\Content\ContentData\Body::UUID,
    ];

    const PARENT_UUIDS = [
        Root::UUID
    ];

}

