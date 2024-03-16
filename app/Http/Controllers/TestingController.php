<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestingController extends Controller {

    public function echoResponse(Request $request)  {
        return response()->json(['status'=>true,'message'=>'ok','data'=>$request->all()]);
    }
}
