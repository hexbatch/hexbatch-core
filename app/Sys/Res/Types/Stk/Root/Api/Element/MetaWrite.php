<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;

#[HexbatchTitle( title: "Write live meta to selected elements")]
#[HexbatchBlurb( blurb: "Set admin can call")]
#[HexbatchDescription( description: "

")]
class MetaWrite extends Api\ElementApi
{
    const UUID = '88382977-683b-41ff-9f99-341b73e54cbc';
    const TYPE_NAME = 'api_element_meta_write';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\MetaWriteAttributes::class,
    ];

}

