<?php

namespace App\Http\Controllers\Web;



use App\Models\ElementType;
use App\OpenApi\Results\Types\TypeResponse;
use App\Sys\Build\SystemResources;

class TestController
{

    public function test() {
        SystemResources::build();
    }
}
