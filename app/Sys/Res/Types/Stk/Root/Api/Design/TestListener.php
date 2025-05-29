<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TestListener extends Api\DesignApi
{
    const UUID = 'ed3b7956-faa7-4dcb-b2ed-af1f7479927b';
    const TYPE_NAME = 'api_design_test_listener';





    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignListenerTest::class,
    ];

}

