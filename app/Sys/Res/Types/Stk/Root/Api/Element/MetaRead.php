<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;

#[HexbatchTitle( title: "Read live meta from selected elements")]
#[HexbatchBlurb( blurb: "Set member can call, and read attributes from selected elements")]
#[HexbatchDescription( description: "
")]
class MetaRead extends Api\ElementApi
{
    const UUID = '0558279b-e927-4848-953a-2dac70311f85';
    const TYPE_NAME = 'api_element_meta_read';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\MetaReadAttributes::class,
    ];

}

