<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Element;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;

#[HexbatchTitle( title: "Remove live from selected elements")]
#[HexbatchBlurb( blurb: "Set admin can call, and remove any selected live from selected elements")]
#[HexbatchDescription( description: "
  Live can be removed manually, not just in triggers. No permission check except caller needs to be set admin
")]
class RemoveLive extends Api\ElementApi
{
    const UUID = '18f455f3-39b0-4c84-92e1-21eb6af0236d';
    const TYPE_NAME = 'api_remove_live';





    const PARENT_CLASSES = [
        Api\ElementApi::class,
        Act\Cmd\Ele\LiveTypeRemove::class,
    ];

}

