<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Path;

use App\Sys\Res\Types\BaseType;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;


class TestPart extends BaseType
{
    const UUID = 'a4246411-9acb-40ee-a38b-5b9bd80011d8';
    const TYPE_NAME = 'api_path_test_part';



    const ATTRIBUTE_CLASSES = [

    ];

    const PARENT_CLASSES = [
        Api\PathApi::class,
        Act\Cmd\PathPartTest::class,
    ];

}

