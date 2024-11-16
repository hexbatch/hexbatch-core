<?php

namespace App\Http\Controllers\Web;



use App\Sys\Build\SystemResources;

class TestController
{
    /**
     * @return void
     * @throws \Exception
     */
    public function test() {

        dd(SystemResources::build());
    }
}
