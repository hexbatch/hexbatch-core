<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;

#[HexbatchTitle( title: "Destroy a location")]
#[HexbatchBlurb( blurb: "Location bounds can be deleted if not used by any published type")]
#[HexbatchDescription( description:'')]
class DestroyLocation extends Api\DesignApi
{
    const UUID = '375b019a-399e-420b-b48c-747c3319115e';
    const TYPE_NAME = 'api_design_location_destroy';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationDestroy::class,
    ];

}

