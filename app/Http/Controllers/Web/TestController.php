<?php

namespace App\Http\Controllers\Web;


use App\Helpers\Annotations\Documentation\HexbatchDescription;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\ApiMapper;
use App\Sys\Build\AttributeMapper;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\SetCreate;

class TestController
{
    /**
     * @return void
     * @throws \Exception
     */
    public function test() {
        ApiMapper::writeToStandardFile();
    }
}
