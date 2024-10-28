<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TestListenerRule extends BaseType
{
    const UUID = 'ed3b7956-faa7-4dcb-b2ed-af1f7479927b';
    const TYPE_NAME = 'api_design_test_listener_rule';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignRuleTest::class,
    ];

}

