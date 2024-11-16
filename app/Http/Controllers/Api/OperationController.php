<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Annotations\Access\TypeOfAccessMarker;
use App\Helpers\Annotations\ApiAccessMarker;
use App\Helpers\Annotations\ApiEventMarker;
use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;
use OpenApi\Attributes as OA;
use App\Sys\Res\Types\Stk\Root\Evt;

class OperationController extends Controller {


    #[OA\Post(
        path: '/api/v1/{namespace}/operations/unshift',
        operationId: 'core.operations.unshift',
        description: "namespace members of the sets can select one or more elements from one set to be unshifted to another ",
        summary: 'Add elements to the front of the set',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker( Evt\Set\SetLeave::class)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Operation\Unshift::class)]
    public function op_unshift() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/operations/combine',
        operationId: 'core.operations.combine',
        description: "namespace members of the sets can adjust set contents using logic operations ",
        summary: 'Add or remove elements between sets',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker( Evt\Set\SetLeave::class)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Operation\Combine::class)]
    public function op_combine() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/operations/mutual',
        operationId: 'core.operations.mutual',
        description: "Any sets and elements can have mutuals made from the readable attributes ",
        summary: 'Make a mutual from one or more sets',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Operation\Mutual::class)]
    public function op_mutual() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/operations/pop',
        operationId: 'core.operations.pop',
        description: "Namespace members of the sets can pop off elements to another set ",
        summary: 'Pops elements from sets',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker( Evt\Set\SetLeave::class)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Operation\Pop::class)]
    public function op_pop() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/operations/push',
        operationId: 'core.operations.push',
        description: "Namespace members of the sets can push elements on the top positions of target sets ",
        summary: 'Push elements to sets',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker( Evt\Set\SetLeave::class)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Operation\Push::class)]
    public function op_push() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/operations/shift',
        operationId: 'core.operations.shift',
        description: "Namespace members of the sets can remove elements from the bottom positions of target sets ",
        summary: 'shift elements from sets',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker( Evt\Set\SetLeave::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Operation\Shift::class)]
    public function op_shift() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
