<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class OperationController extends Controller {
    #[ApiTypeMarker( Root\Api\Operation\Unshift::class)]
    public function op_unshift() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Operation\Combine::class)]
    public function op_combine() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Operation\Mutual::class)]
    public function op_mutual() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Operation\Pop::class)]
    public function op_pop() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Operation\Push::class)]
    public function op_push() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Operation\Shift::class)]
    public function op_shift() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
