<?php

namespace App\Http\Controllers\Web;



use App\Models\ElementType;
use App\OpenApi\Types\TypeResponse;

class TestController
{

    public function test() {
        $type = ElementType::getElementType(id:585);
        return response()->json(new TypeResponse(given_type: $type));
    }
}
