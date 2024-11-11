<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class ServerController extends Controller {
    #[ApiTypeMarker( Root\Api\Server\Show::class)]
    public function us() {
        //todo this is public access, should just be public attributes on the server type element: about, domain, version, commit hash,home_url
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Server\Edit::class)]
    public function edit_server() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

}
