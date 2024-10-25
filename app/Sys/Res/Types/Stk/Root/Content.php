<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stk\Root;

use App\Sys\Res\Atr\Stk\MetaData\Content\ContentData;
use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class Content extends BaseType
{
    const UUID = 'c9f9671e-905f-4198-9049-ef0e1ad4d268';
    const TYPE_NAME = 'content';



    const ATTRIBUTE_CLASSES = [
        ContentData::class,
        \App\Sys\Res\Atr\Stk\MetaData\Content\ContentData\Subject::class,
        ContentData\Title::class,
        \App\Sys\Res\Atr\Stk\MetaData\Content\ContentData\Tags::class,
        ContentData\Blurb::class,
        \App\Sys\Res\Atr\Stk\MetaData\Content\ContentData\Body::class,
    ];

    const PARENT_CLASSES = [
        Root::class
    ];

}

