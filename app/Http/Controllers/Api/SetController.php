<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Phase;
use App\OpenApi\Callbacks\HexbatchCallbackCollectionResponse;
use App\OpenApi\Elements\ElementCollectionResponse;
use App\OpenApi\Params\Set\AddElementParams;
use App\OpenApi\Params\Type\CreateElementParams;
use App\OpenApi\Resources\HexbatchNamespace;

use App\OpenApi\Resources\HexbatchResource;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class SetController extends Controller {


    /**
     * @throws \Exception
     */
    #[OA\Post(
        path: '/api/v1/{namespace}/sets/add_element',
        operationId: 'core.sets.add_element',
        description: "Element namespace members can put element into any set that allows that ",
        summary: 'Change the element owner',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: AddElementParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Elements added', content: new JsonContent(ref: ElementCollectionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetEnter::class)]
    #[ApiEventMarker(Evt\Set\ShapeEnter::class)]
    #[ApiEventMarker(Evt\Set\MapEnter::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_ADMIN)]
    #[ApiTypeMarker( Root\Api\Set\AddElement::class)]
    public function add_element(Request $request,ElementSet $set) {
        $params = new AddElementParams(given_set: $set);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Set\AddElement(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['add-elements']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }





    #[OA\Delete(
        path: '/api/v1/{namespace}/sets/destroy',
        operationId: 'core.sets.destroy_set',
        description: "Set owners can destroy their sets, bypassing the leave event, can be blocked by handlers on the type ",
        summary: 'Destroys the set, keeps the element',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\SetDestroyed::class)]
    #[ApiEventMarker( Evt\Server\SetDestroying::class)]
    #[ApiEventMarker( Evt\Set\SetChildDestroyed::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_OWNER)]
    #[ApiTypeMarker( Root\Api\Set\DestroySet::class)]
    public function destroy_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/sets/remove_element',
        operationId: 'core.sets.remove_element',
        description: "Element namespace members can put element into any set that allows that ",
        summary: 'Change the element owner',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetLeave::class)]
    #[ApiEventMarker(Evt\Set\ShapeLeave::class)]
    #[ApiEventMarker(Evt\Set\MapLeave::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosingEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosingEnd::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\RemoveElement::class)]
    public function remove_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/sets/empty_set',
        operationId: 'core.sets.empty_set',
        description: "Element namespace members can clear out sticky elements. Event handlers can block their elements from leaving ",
        summary: 'Removes all elements except sticky ones',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\EmptySet::class)]
    public function empty_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{namespace}/sets/stick_element',
        operationId: 'core.sets.stick_element',
        description: "Set namespace members can make sticky elements in those sets ",
        summary: 'Makes element sticky in set operations',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\StickElement::class)]
    public function stick_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{namespace}/sets/unstick_element',
        operationId: 'core.sets.unstick_element',
        description: "Set namespace members can unstick elements in those sets ",
        summary: 'Unsticks one or more elements in one or more sets',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\UnstickElement::class)]
    public function unstick_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/sets/purge',
        operationId: 'core.sets.purge_set',
        description: "System can remove sets without events or permission ",
        summary: 'System can delete any set',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Set\Purge::class)]
    public function purge_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Delete(
        path: '/api/v1/{namespace}/sets/purge_member',
        operationId: 'core.sets.purge_member',
        description: "System can remove elements without events from any set ",
        summary: 'System can remove set contents',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Set\PurgeMember::class)]
    public function purge_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/{namespace}/sets/show',
        operationId: 'core.sets.show_set',
        description: "Shows information about a set to set members ",
        summary: 'Gives information about a set',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\Show::class)]
    public function show_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/sets/public',
        operationId: 'core.sets.show_public',
        description: "Anyone can see public information ",
        summary: 'Shows a public view of this set',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Set\ShowPublic::class)]
    public function show_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/{namespace}/sets/list_children',
        operationId: 'core.sets.list_children',
        description: "Set namespace members can get a list of children sets ",
        summary: 'List child sets',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListChildren::class)]
    public function list_children() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/sets/list_elements',
        operationId: 'core.sets.list_elements',
        description: "Set namespace members can get a list of set contents ",
        summary: 'list elements in a set',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListMembers::class)]
    public function list_elements() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/sets/list',
        operationId: 'core.sets.list',
        description: "Lists all sets this is a member, admin or owner  ",
        summary: 'list sets',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListSets::class)]
    public function list_sets() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

}

