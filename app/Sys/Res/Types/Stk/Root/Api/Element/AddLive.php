<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


#[HexbatchTitle( title: "Add live to selected elements")]
#[HexbatchBlurb( blurb: "Set admin can call, and if permissions ok, will add a live. If any permissions not ok, all fail")]
#[HexbatchDescription( description: "
  Live can be added manually, not just in triggers.
")]
class AddLive extends Api\ElementApi
{
    const UUID = 'e5c47fc2-e128-4912-b546-6d78b0420f90';
    const TYPE_NAME = 'api_element_add_live';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\LiveTypeAdd::class,
    ];

}

