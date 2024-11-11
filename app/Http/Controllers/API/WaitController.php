<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class WaitController extends Controller {
    #[ApiTypeMarker( Root\Api\Waiting\ShowMaster::class)]
    public function show_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\CreateMaster::class)]
    public function create_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\UpdateMaster::class)]
    public function update_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\ShowMasterRun::class)]
    public function show_master_run() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\ShowMasterPending::class)]
    public function show_master_pending() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\RunMaster::class)]
    public function run_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\ListMasters::class)]
    public function list_masters() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\ShowSemaphore::class)]
    public function show_semaphore() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\ListSemaphores::class)]
    public function list_semaphores() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\Reset::class)]
    public function reset_semaphore() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\Ready::class)]
    public function ready_semaphore() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Waiting\ListWaits::class)]
    public function list_waits() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Waiting\ListMutexes::class)]
    public function list_mutexes() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Waiting\ShowMutex::class)]
    public function show_mutexes() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

}
