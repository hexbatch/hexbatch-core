<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


#[HexbatchTitle( title: "Destroy a schedule")]
#[HexbatchBlurb( blurb: "Schdules can be destroyed if not used")]
#[HexbatchDescription( description:'')]

class DestroyTime extends Api\DesignApi
{
    const UUID = 'd55e0d09-0830-4723-acbc-acb3595b7d57';
    const TYPE_NAME = 'api_design_destroy_time';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignTimeDestroy::class,
    ];

}

