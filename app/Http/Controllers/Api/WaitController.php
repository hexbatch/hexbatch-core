<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class WaitController extends Controller {



    #[OA\Post(
        path: '/api/v1/{namespace}/waits/create_master',
        operationId: 'core.waits.create_master',
        description: "A new master system is created with the caller the owner of the new types and sets and elements",
        summary: 'Create a master system',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeOwnerChange::class)]
    #[ApiEventMarker( Evt\Server\SetCreated::class)]
    #[ApiEventMarker( Evt\Server\DesignPending::class)]
    #[ApiEventMarker( Evt\Server\TypePublished::class)]
    #[ApiEventMarker( Evt\Type\ElementCreation::class)]
    #[ApiEventMarker( Evt\Type\ElementCreationBatch::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[ApiTypeMarker( Root\Api\Waiting\CreateMaster::class)]
    public function create_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/waits/update_master',
        operationId: 'core.waits.update_master',
        description: "Changes the ready state of a master",
        summary: 'Update waiting master state',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetLeave::class,)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Waiting\UpdateMaster::class)]
    public function update_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/waits/run_master',
        operationId: 'core.waits.run_master',
        description: "Sets a pending master to run, any member of the type can do this. May have to wait, also may not work if limits set",
        summary: 'Starts running a master',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetLeave::class,)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\RunMaster::class)]
    public function run_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/waits/reset_semaphore',
        operationId: 'core.waits.reset_semaphore',
        description: "Semaphores moved to the idle state",
        summary: 'Semaphores moved to idle',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetLeave::class,)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Waiting\Reset::class)]
    public function reset_semaphore() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/waits/ready_semaphore',
        operationId: 'core.waits.ready_semaphore',
        description: "Semaphores moved to the waiting state",
        summary: 'Sets semaphores ready to be used',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetLeave::class,)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Waiting\Ready::class)]
    public function ready_semaphore() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{namespace}/waits/show_master',
        operationId: 'core.waits.show_master',
        description: "Type members can see the status of the master",
        summary: 'Shows information about a master',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\ShowMaster::class)]
    public function show_master() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/waits/show_master_run',
        operationId: 'core.waits.show_master_run',
        description: "Shows the details about a run the master made, or current state if running",
        summary: 'Shows master run data',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\ShowMasterRun::class)]
    public function show_master_run() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/waits/list_masters',
        operationId: 'core.waits.list_masters',
        description: "Lists the masters the caller is a member, admin or owner of",
        summary: 'List masters',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\ListMasters::class)]
    public function list_masters() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/waits/show_semaphore',
        operationId: 'core.waits.show_semaphore',
        description: "Shows information about a semaphore if the caller is a member, admin or owner",
        summary: 'Show semaphore',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\ShowSemaphore::class)]
    public function show_semaphore() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/waits/list_semaphores',
        operationId: 'core.waits.list_semaphores',
        description: "Lists the semaphores the caller is a member, admin or owner of",
        summary: 'List semaphores',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\ListSemaphores::class)]
    public function list_semaphores() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/waits/list_waits',
        operationId: 'core.waits.list_waits',
        description: "Lists the semaphores or mutexes being waited on from types the caller is a member, admin or owner of",
        summary: 'List Waits',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Waiting\ListWaits::class)]
    public function list_waits() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/waits/show_wait',
        operationId: 'core.waits.show_wait',
        description: "Shows information specific wait on a type the caller is a member, admin or owner",
        summary: 'Show wait info',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\ShowWait::class)]
    public function show_wait() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/waits/list_mutexes',
        operationId: 'core.waits.list_mutexes',
        description: "Lists the mutexes created with types the caller is a member, admin or owner of",
        summary: 'List Mutexes',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Waiting\ListWaits::class)]
    public function list_mutexes() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/waits/show_mutex',
        operationId: 'core.waits.show_mutex',
        description: "Shows information about a mutex if the caller is a member, admin or owner",
        summary: 'Show mutex',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\ShowMutex::class)]
    public function show_mutex() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/waits/wait_if_available',
        operationId: 'core.waits.wait_if_available',
        description: "Will wait only if this is immediately ready, otherwise it fails",
        summary: 'Do work if signal ready, otherwise do not wait',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\WaitSuccess::class)]
    #[ApiEventMarker( Evt\Server\WaitFail::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\WaitIfAvailable::class)]
    public function wait_if_available() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/waits/wait_for_any',
        operationId: 'core.waits.wait_for_any',
        description: "Will for only the first signal ready and ignore the other waits after that. Can set many signals to wait",
        summary: 'Do work at first signal that is ready',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\WaitSuccess::class)]
    #[ApiEventMarker( Evt\Server\WaitFail::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\WaitForAny::class)]
    public function wait_for_any() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/waits/wait_for_all',
        operationId: 'core.waits.wait_for_all',
        description: "Will for all the signals to be ready. Can set many signals to wait",
        summary: 'Do work after all signals are ready',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\WaitSuccess::class)]
    #[ApiEventMarker( Evt\Server\WaitFail::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\WaitForAny::class)]
    public function wait_for_all() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/waits/wait_for_mutex',
        operationId: 'core.waits.wait_for_mutex',
        description: "Will wait for this mutex",
        summary: 'Do work after mutex allows',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\WaitSuccess::class)]
    #[ApiEventMarker( Evt\Server\WaitFail::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\WaitForMutex::class)]
    public function wait_for_mutex() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/waits/wait_for_semaphore',
        operationId: 'core.waits.wait_for_semaphore',
        description: "Will wait on the semaphore",
        summary: 'Do work when semaphore allows',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\WaitSuccess::class)]
    #[ApiEventMarker( Evt\Server\WaitFail::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Waiting\WaitForSemaphore::class)]
    public function wait_for_semaphore() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



}
