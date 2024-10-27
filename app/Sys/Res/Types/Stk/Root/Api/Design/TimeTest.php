<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TimeTest extends BaseType
{
    const UUID = '375b019a-399e-420b-b48c-747c3319115e';
    const TYPE_NAME = 'api_design_time_test';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\DesignTimeTest::class,
    ];

}

