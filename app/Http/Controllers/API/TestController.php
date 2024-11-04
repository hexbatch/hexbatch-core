<?php

namespace App\Http\Controllers\API;



use App\Sys\Build\ApiMapper;

class TestController
{
    public function test() {
        ApiMapper::writeToStandardFile();
    }
}
