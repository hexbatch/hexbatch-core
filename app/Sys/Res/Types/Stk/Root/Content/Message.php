<?php
//  this is some news or message


namespace App\Sys\Res\Types\Stk\Root\Content;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Content;

class Message extends BaseType
{
    const UUID = 'e7d58805-4e49-4deb-a348-b3bd5df1860f';
    const TYPE_NAME = 'message';



    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Content::class
    ];

}

