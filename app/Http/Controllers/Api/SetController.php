<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Data\ApiParams\Data\Elements\Params\SelectElementParamData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Data\ApiParams\Data\ErrorData;
use App\Data\ApiParams\Data\Sets\Params\AddElementsParamData;
use App\Data\ApiParams\Data\Sets\Responses\SetList;
use App\Data\ApiParams\Data\Sets\SetData;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchNamespace;
use App\Data\ApiParams\OpenApi\Common\Resources\HexbatchResource;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\ElementSet;
use App\Models\Phase;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Thangs\Data\ThangData;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class SetController extends Controller {


    /**
     * @throws \Exception
     * @throws \Throwable
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/add_element',
        operationId: 'core.sets.add_element',
        description: "Element namespace members can put element into any set they control. Union of elements selected and not there are added ",
        summary: 'Change the element owner',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: AddElementsParamData::class)),
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Elements added', content: new JsonContent(ref: ElementList::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'Set was not found')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetEntering::class)]
    #[ApiEventMarker( Evt\Set\SetEntered::class)]
    #[ApiEventMarker(Evt\Set\ShapeEntered::class)]
    #[ApiEventMarker(Evt\Set\MapEntered::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedStart::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedStart::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_ADMIN)]
    #[ApiTypeMarker( Root\Api\Set\AddElement::class)]
    public function add_element(UserNamespace $namespace,Phase $working_phase,ElementSet $set,Request $request) {
        Utilities::ignoreVar($working_phase);
        $params = AddElementsParamData::fromRequest($request);
        $data_out = Root\Api\Set\AddElement::addElementsToSet(params: $params,calling_namespace: $namespace, given_set: $set,
            is_system: false, tags: ['api-top']);

        if ($data_out instanceof Thang) {
            $data_out = ThangData::from($data_out);
            $http_code = CodeOf::HTTP_CREATED;
        }
        else {
            $http_code = CodeOf::HTTP_ACCEPTED;
            $data_out = SetData::from($data_out);
        }
        return  response()->json($data_out,$http_code);
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

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
        description: "Remove selected elements from set. Union of selected elements and present elements chosen ",
        summary: 'Remove elements from set',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: SelectElementParamData::class)),
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\SetLeaving::class)]
    #[ApiEventMarker( Evt\Set\SetLeft::class)]
    #[ApiEventMarker(Evt\Set\ShapeLeft::class)]
    #[ApiEventMarker(Evt\Set\MapLeft::class)]
    #[ApiEventMarker(Evt\Set\TypeMapEnclosedEnd::class)]
    #[ApiEventMarker(Evt\Set\TypeShapeEnclosedEnd::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_ADMIN)]
    #[ApiTypeMarker( Root\Api\Set\RemoveElement::class)]
    public function remove_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/empty_set',
        operationId: 'core.sets.empty_set',
        description: "Removes all except sticky elements. Event handlers can block their elements from leaving ",
        summary: 'Removes all elements except sticky ones',
        security: [['bearerAuth' => []]],
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_ADMIN)]
    #[ApiTypeMarker( Root\Api\Set\EmptySet::class)]
    public function empty_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{user_namespace}/sets/phase/{working_phase}/set/{element_set}/stick_elements',
        operationId: 'core.sets.stick_element',
        description: "Set namespace admins can stick or unstick elements in those sets ",
        summary: 'Makes element sticky in set operations',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: AddElementsParamData::class)),
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_ADMIN)]
    #[ApiTypeMarker( Root\Api\Set\StickElement::class)]
    public function stick_elements() {
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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

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
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

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
        path: '/api/v1/{user_namespace}/sets/set/{element_set}/show',
        operationId: 'core.sets.show_set',
        description: "Shows information about a set to set members ",
        summary: 'Gives information about a set',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: SelectElementParamData::class)),
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),


            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Elements added', content: new JsonContent(ref: SetData::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'Set was not found')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ShowSet::class)]
    public function show_set(UserNamespace $namespace,ElementSet $set,Request $request ) {
        $params = SelectElementParamData::fromRequest($request);
        $data_out =  Root\Api\Set\ShowSet::showSet(set: $set,params: $params, caller_namespace:$namespace);
        if ($data_out instanceof Thang) {
            $data_out = ThangData::from($data_out);
            $http_code = CodeOf::HTTP_CREATED;
        }
        else {
            $http_code = CodeOf::HTTP_ACCEPTED;
        }
        return  response()->json($data_out,$http_code);
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







    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/sets/set/{element_set}/list_elements',
        operationId: 'core.sets.list_elements',
        description: "Can search in the element list of a set ",
        summary: 'list elements in a set',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: SelectElementParamData::class)),
        tags: ['set','element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Listed elements in this set', content: new JsonContent(ref: ElementList::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue', content: new JsonContent(ref: ErrorData::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListMembers::class)]
    public function list_elements(UserNamespace $namespace,Request $request,ElementSet $set) {
        $params = SelectElementParamData::fromRequest($request);
        $params->set_ref = $set->ref_uuid;
        $data_out = Root\Api\Set\ListMembers::listElements(params: $params, caller_namespace: $namespace);
        return  response()->json($data_out,CodeOf::HTTP_OK);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/sets/list',
        operationId: 'core.sets.list',
        description: "Lists all sets asked for  ",
        summary: 'list sets',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: SelectElementParamData::class)),
        tags: ['set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchNamespace::class) ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the set is in",
                in: 'path', required: true,  schema: new OA\Schema(type: HexbatchResource::class) ),

        ],
        responses: [


            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Showing sets', content: new JsonContent(ref: SetList::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Processing|waiting', content: new JsonContent(ref: ThangData::class)),

            new OA\Response(    response: CodeOf::HTTP_FORBIDDEN, description: 'Not allowed'),
            new OA\Response(    response: CodeOf::HTTP_NOT_FOUND, description: 'Set was not found')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SET_MEMBER)]
    #[ApiTypeMarker( Root\Api\Set\ListSets::class)]
    public function list_sets(UserNamespace $namespace,Request $request) {

        $params = SelectElementParamData::fromRequest($request);
        $data_out =  Root\Api\Set\ListSets::listSets(params: $params, caller_namespace:$namespace);
        if ($data_out instanceof Thang) {
            $data_out = ThangData::from($data_out);
            $http_code = CodeOf::HTTP_CREATED;
        }
        else {
            $http_code = CodeOf::HTTP_ACCEPTED;
        }
        return  response()->json($data_out,$http_code);
    }

}

