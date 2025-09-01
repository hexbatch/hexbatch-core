<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\ElementSet;
use App\Models\Phase;
use App\OpenApi\Common\Resources\HexbatchNamespace;
use App\OpenApi\Common\Resources\HexbatchResource;
use App\OpenApi\Params\Actioning\Set\AddElementParams;
use App\OpenApi\Params\Listing\Elements\ListElementParams;
use App\OpenApi\Params\Listing\Set\ListSetParams;
use App\OpenApi\Params\Listing\Set\ShowSetParams;
use App\OpenApi\Results\Callbacks\HexbatchCallbackCollectionResponse;
use App\OpenApi\Results\Elements\ElementCollectionResponse;
use App\OpenApi\Results\Set\SetCollectionResponse;
use App\OpenApi\Results\Set\SetResponse;
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/add_element',
        operationId: 'core.sets.add_element',
        description: "Element namespace members can put element into any set that allows that ",
        summary: 'Change the element owner',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: AddElementParams::class)),
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Elements added', content: new JsonContent(ref: ElementCollectionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/destroy',
        operationId: 'core.sets.destroy_set',
        description: "Set owners can destroy their sets, bypassing the leave event, can be blocked by handlers on the type ",
        summary: 'Destroys the set, keeps the element',
        security: [['bearerAuth' => []]],
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/remove_element',
        operationId: 'core.sets.remove_element',
        description: "Element namespace members can put element into any set that allows that ",
        summary: 'Change the element owner',
        security: [['bearerAuth' => []]],
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/empty_set',
        operationId: 'core.sets.empty_set',
        description: "Element namespace members can clear out sticky elements. Event handlers can block their elements from leaving ",
        summary: 'Removes all elements except sticky ones',
        security: [['bearerAuth' => []]],
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/stick_element',
        operationId: 'core.sets.stick_element',
        description: "Set namespace members can make sticky elements in those sets ",
        summary: 'Makes element sticky in set operations',
        security: [['bearerAuth' => []]],
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/unstick_element',
        operationId: 'core.sets.unstick_element',
        description: "Set namespace members can unstick elements in those sets ",
        summary: 'Unsticks one or more elements in one or more sets',
        security: [['bearerAuth' => []]],
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/purge_set',
        operationId: 'core.sets.purge_set',
        description: "System can remove sets without events or permission ",
        summary: 'System can delete any set',
        security: [['bearerAuth' => []]],
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/purge_members',
        operationId: 'core.sets.purge_members',
        description: "System can remove elements without events from any set ",
        summary: 'System can remove set contents',
        security: [['bearerAuth' => []]],
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Set\PurgeMember::class)]
    public function purge_members() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/show',
        operationId: 'core.sets.show_set',
        description: "Shows information about a set to set members ",
        summary: 'Gives information about a set',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ShowSetParams::class)),
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Set info returned', content: new JsonContent(ref: SetResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ShowSet::class)]
    public function show_set(Request $request, ElementSet $set) {
        $params = new ShowSetParams(given_set: $set);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Set\ShowSet(params: $params, is_async: false, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['show-set']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getOwnResponse();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }




    #[OA\Get(
        path: '/api/v1/sets/public',
        operationId: 'core.sets.show_public',
        description: "Anyone can see public information ",
        summary: 'Shows a public view of this set',
        tags: ['set','public'],
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
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/list_children',
        operationId: 'core.sets.list_children',
        description: "Set namespace members can get a list of children sets ",
        summary: 'List child sets',
        security: [['bearerAuth' => []]],
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListChildren::class)]
    public function list_children() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/list_elements',
        operationId: 'core.sets.list_elements',
        description: "Can search in the element list of a set ",
        summary: 'list elements in a set',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListElementParams::class)),
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Listed elements in this set', content: new JsonContent(ref: ElementCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListMembers::class)]
    public function list_elements(Request $request,ElementSet $set) {
        $params = new ListElementParams(given_set: $set);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Set\ListMembers(params: $params, given_set: $set, is_async: false, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['list-members']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getOwnResponse();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/list',
        operationId: 'core.sets.list',
        description: "Lists all sets this is a member, admin or owner  ",
        summary: 'list sets',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListSetParams::class)),
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Listed sets', content: new JsonContent(ref: SetCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListSets::class)]
    public function list_sets(Phase $working_phase,Request $request) {
        $params = new ListSetParams(working_phase: $working_phase);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Set\ListSets(params: $params, is_async: false, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['list-sets']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getOwnResponse();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }

}

