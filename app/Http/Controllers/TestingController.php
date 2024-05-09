<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestingController extends Controller {

    public function echoResponse(Request $request)  {
        $code = $request->query->getInt('return_code',200);
        return response()->json(['status'=>true,'message'=>'ok','data'=>$request->all()],$code);
    }

    public function echoPost(Request $request)  {
        Log::debug("echoPost is ",['laravel'=>$request->request->all(),'plain'=>$_REQUEST,'input'=>file_get_contents('php://input')]);
        Log::debug("echoPost Headers are ",$request->headers->all());
        /**
         * @var UploadedFile $a_file
         */
        foreach ($request->files->all() as $a_file) {
            Log::debug("echoPost file name ". $a_file->getClientOriginalName() ,['body'=>$a_file->getContent()]);
        }
        return response()->json(['status'=>true,'message'=>'ok post','data'=>$request->all()]);
    }
}
