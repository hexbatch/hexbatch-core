<?php

namespace App\Http\Controllers\API;

use App\Sys\Build\LoadStatic;

class TestController
{
    public function test() {
         new LoadStatic();
    }
}
