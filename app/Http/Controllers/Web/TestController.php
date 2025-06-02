<?php

namespace App\Http\Controllers\Web;



use App\Sys\Build\SystemResources;

class TestController
{

    public function test() {

        $newly = SystemResources::build();
        dd($newly);
    }
}
