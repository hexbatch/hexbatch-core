<?php

namespace App\Http\Controllers\API;


use App\Helpers\Standard\StandardAttributes;
use App\Http\Controllers\Controller;

use App\Http\Resources\StandardAttributeCollection;


//todo rm delete this, no attribute api
/*
| Get    | attribute/:id/bounds/ping   |            | Determines if the attribute is in bounds              | Location, Time and Set                                                |
 */

class StandardController extends Controller
{

    public function attribute_list_standard() {
        $standard = StandardAttributes::getAttributeCache();
        return response()->json(new StandardAttributeCollection($standard), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
}
