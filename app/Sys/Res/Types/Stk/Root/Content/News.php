<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stk\Root\Content;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Content;

class News extends BaseType
{
    const UUID = 'f1d04677-c949-4014-b7d1-3f2a9cd03c1f';
    const TYPE_NAME = 'news';



    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Content::class
    ];

}

