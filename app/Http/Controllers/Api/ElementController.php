<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\Phase;
use App\OpenApi\Common\Resources\HexbatchNamespace;
use App\OpenApi\Common\Resources\HexbatchResource;
use App\OpenApi\Params\Actioning\Element\ChangeElementOwnerParams;
use App\OpenApi\Params\Actioning\Element\ElementSelectParams;
use App\OpenApi\Params\Actioning\Element\LinkCreateParams;
use App\OpenApi\Params\Actioning\Set\SetCreateParams;
use App\OpenApi\Params\Listing\Elements\ListElementParams;
use App\OpenApi\Params\Listing\Elements\ShowElementParams;
use App\OpenApi\Results\Callbacks\HexbatchCallbackCollectionResponse;
use App\OpenApi\Results\Elements\ElementActionResponse;
use App\OpenApi\Results\Elements\ElementCollectionResponse;
use App\OpenApi\Results\Elements\ElementResponse;
use App\OpenApi\Results\Set\LinkResponse;
use App\OpenApi\Results\Set\SetResponse;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class ElementController extends Controller {

    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/elements/{element}/change_owner',
        operationId: 'core.elements.change_owner',
        description: "Element owner can give ownership to another namespace at any time. Any number of elements can be included with a path ",
        summary: 'Change the element owner',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ChangeElementOwnerParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),
        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Owner changed', content: new JsonContent(ref: ElementCollectionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementOwnerChange::class)]
    #[ApiEventMarker( Evt\Type\ElementRecieved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_OWNER)]
    #[ApiTypeMarker( Root\Api\Element\ChangeOwner::class)]
    public function change_owner(Request $request) {
        $params = new ChangeElementOwnerParams();
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\ChangeOwner(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['change-element-owner']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/type_off',
        operationId: 'core.elements.type_off',
        description: "Element admin group turn off attributes in groups of subtype (parent types) in elements inside sets given by a path ",
        summary: 'Turn off all the subtype attributes of elements',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ElementSelectParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Type on', content: new JsonContent(ref: ElementActionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementTypeTurningOff::class)]
    #[ApiEventMarker( Evt\Set\ElementTypeTurnedOff::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\TypeOff::class)]
    public function type_off(Phase $working_phase, Request $request) {
        $params = new ElementSelectParams(given_phase: $working_phase);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\TypeOn(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['type-off']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/type_on',
        operationId: 'core.elements.type_on',
        description: "Element admin group turn on all parent type attributes. The types, elements and sets given by a path ",
        summary: 'Turn on all the attributes of a parent type in elements',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ElementSelectParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Type off', content: new JsonContent(ref: ElementActionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementTypeTurningOn::class)]
    #[ApiEventMarker( Evt\Set\ElementTypeTurnedOn::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\TypeOn::class)]
    public function type_on(Phase $working_phase, Request $request) {
        $params = new ElementSelectParams(given_phase: $working_phase);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\TypeOn(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['type-on']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/read_attribute',
        operationId: 'core.elements.read_attribute',
        description: "Can select the same attribute(s) in elements(s) to read, ".
                    "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read the same attributes in one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\Reading::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Element\ReadAttribute::class)]
    public function read_attribute(Phase $working_phase, Element $element, Request $request) {
        $params = new ElementSelectParams(elements: [$element], given_phase: $working_phase);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\TypeOn(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['read-attribute']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    #[OA\Get(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/read_live_type',
        operationId: 'core.elements.read_live_type',
        description: "This is the same as core.elements.read_type but looks at the live and ignores the inherited",
        summary: 'Read all the attributes of a type in one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\Reading::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Element\ReadLiveType::class)]
    public function read_live_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/read_type',
        operationId: 'core.elements.read_type',
        description:  "Can select the same type(s) in elements(s) to read, will either succeed if can read all of them or fail if one cannot be read ".
            "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read all the attributes of a live type in one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\Reading::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Element\ReadLiveType::class)]
    public function read_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     */
    #[OA\Patch(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/write_attribute',
        operationId: 'core.elements.write_attribute',
        description: "Write one or more elements found in a path, that have the same attributes. If one can. ",
        summary: 'Write json to the same attributes of one or more elements',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ElementSelectParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Write attribute', content: new JsonContent(ref: ElementActionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Set\AttributeWrite::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Element\WriteAttribute::class)]
    public function write_attribute(Phase $working_phase, Element $element, Request $request) {
        $params = new ElementSelectParams(elements: [$element], given_phase: $working_phase);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\TypeOn(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['write-attribute']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }




    #[OA\Get(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/read_time',
        operationId: 'core.elements.read_time',
        description: "Can read current or next time span of the element's type, its parents and applied live ".
        "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read the locations of one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiEventMarker( Evt\Element\ReadingTime::class)]
    #[ApiTypeMarker( Root\Api\Element\ReadTime::class)]
    public function read_time() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }







    #[OA\Post(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/live/add',
        operationId: 'core.elements.add_live',
        description: "If can read any attribute on a type, can add it as a live part to one or more elements in a search path.".
                "\n If not part of the element ns, can still add it if part of the type ns and event listeners allow  ",
        summary: 'Add a live type to one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['element','live'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\LiveTypeAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\AddLive::class)]
    public function add_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }








    #[OA\Post(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/live/{live_type}/copy',
        operationId: 'core.elements.copy_live',
        description: "If can read any attribute on both the source and destination type, can copy its and its state to a new element.".
        "\n If not part of the element ns, can still add it if part of the type ns and event listeners allow  ",
        summary: 'Copy a live type and its state to one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['element','live'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'live_type', description: "The live type",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\LiveTypePasted::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\CopyLive::class)]
    public function copy_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }







    #[OA\Delete(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/live/{live_type}/remove',
        operationId: 'core.elements.remove_live',
        description: "If can read any attribute on the live type, can remove its and its state to a new element.".
        "\n If not part of the element ns, can still remove it if part of the type ns and event listeners allow  ",
        summary: 'Remove a live type from one or more elements',
        security: [['bearerAuth' => []]],
        tags: ['element','live'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'live_type', description: "The live type",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\LiveTypeRemoved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\RemoveLive::class)]
    public function remove_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/live/promote',
        operationId: 'core.elements.promote_live',
        description: "System can add live types without permisision. No events ",
        summary: 'System adds live types',
        security: [['bearerAuth' => []]],
        tags: ['element','live'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),


        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\PromoteLive::class)]
    public function promote_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Delete(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/live/{live_type}/demote',
        operationId: 'core.elements.demote_live',
        description: "System can remove live types from elements without permisision. No events ",
        summary: 'System subtracts live types',
        security: [['bearerAuth' => []]],
        tags: ['element','live'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'live_type', description: "The live type",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\DemoteLive::class)]
    public function demote_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/ping',
        operationId: 'core.elements.ping',
        description: "Element ns can use that to ping elements to target sets.",
        summary: 'Ping one or more elements to a set',
        security: [['bearerAuth' => []]],
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\Ping::class)]
    public function ping_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/elements/{element}/destroy',
        operationId: 'core.elements.destroy_element',
        description: "Element admin can destroy one or more elements, the type or parent types can reject this",
        summary: 'Destroys one or more elements',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ElementSelectParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Elements destroyed', content: new JsonContent(ref: ElementCollectionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementDestruction::class)]
    #[ApiEventMarker( Evt\Type\ElementDestroyed::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\Destroy::class)]
    public function destroy_element(Request $request) {
        $params = new ElementSelectParams();
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\Destroy(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['destroy-elements']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Delete(
        path: '/api/v1/{user_namespace}/elements/{element}/purge',
        operationId: 'core.elements.purge_element',
        description: "System can destroy one or more elements without permission or events",
        summary: 'System Destroy one or more elements',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ElementSelectParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Elements purged', content: new JsonContent(ref: ElementCollectionResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\Purge::class)]
    public function purge_element(Request $request) {
        $params = new ElementSelectParams();
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\Purge(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['purge-elements']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/link/{element_set}',
        operationId: 'core.elements.link',
        description: "Anyone can make a link from an element they administer to a target set, or sets. The element does not have to belong to the set ".
        "\n The link can be assigned to another namespace, they can reject that. The linked set can reject the link",
        summary: 'Makes a link between an element and a set',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: LinkCreateParams::class)),
        tags: ['element','set','link'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element_set', description: "The set",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Link created', content: new JsonContent(ref: LinkResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Server\LinkCreated::class)]
    #[ApiEventMarker( Evt\Server\LinkCreating::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\Link::class)]
    public function create_link(Request $request,Element $element,ElementSet $set) {
        $params = new LinkCreateParams(given_element: $element,given_set: $set);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\Link(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['create-link']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/show',
        operationId: 'core.elements.show_element',
        description: "Element members can see details about an element",
        summary: 'Shows the value and information about an element',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ShowElementParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Element info returned', content: new JsonContent(ref: ElementResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ShowElement::class)]
    public function show_element(Element $element,Request $request) {
        $params = new ShowElementParams(given_element: $element);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\ShowElement(params: $params, is_async: false, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['show-element']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getOwnResponse();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }


    /**
     * @throws \Exception
     */
    #[OA\Get(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/list',
        operationId: 'core.elements.list_elements',
        description: "Element members can see a list of all the elements of namespaces they belong",
        summary: 'Shows list of elements',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: ListElementParams::class)),
        tags: ['element'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Listed elements', content: new JsonContent(ref: ElementCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ListElements::class)]
    public function list_elements(Phase $working_phase,Request $request) {
        $params = new ListElementParams(working_phase: $working_phase);
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\ListElements(params: $params, is_async: false, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['list-elements']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getOwnResponse();
        return  response()->json(['response'=>$data_out],$api->getCode());
    }



    #[OA\Get(
        path: '/api/v1/elements/public',
        operationId: 'core.elements.show_public',
        description: "Anyone can see public information about an element, attributes that are marked as public will show their data.",
        summary: 'Shows public information about an element',
        security: [['bearerAuth' => []]],
        tags: ['element','public'],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Element\ShowPublic::class)]
    public function show_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * @throws \Exception
     */
    #[OA\Post(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/create_set',
        operationId: 'core.elements.create_set',
        description: "Element namespace admins can create sets out of those elements. Inheritied types can deny. Sets can be created a children of other sets",
        summary: 'Create a set from element',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: SetCreateParams::class)),
        tags: ['element','set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Attribute created', content: new JsonContent(ref: SetResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiEventMarker( Evt\Server\SetCreated::class)]
    #[ApiEventMarker( Evt\Set\SetChildCreated::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\CreateSet::class)]
    public function create_set(Request $request,Element $element) {
        $params = new SetCreateParams(given_element: $element,namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Root\Api\Element\CreateSet(params: $params, is_async: true, tags: ['api-top']);
        $thing = $api->createThingTree(tags: ['create-set']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    #[OA\Post(
        path: '/api/v1/{user_namespace}/elements/phase/{working_phase}/element/{element}/promote_set',
        operationId: 'core.elements.promote_set',
        description: "System can make sets from any element without permisision. No events ",
        summary: 'System can make sets',
        security: [['bearerAuth' => []]],
        tags: ['element','set'],
        parameters: [
            new OA\PathParameter(  name: 'user_namespace', description: "Namespace this is run under",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchNamespace') ),

            new OA\PathParameter(  name: 'working_phase', description: "The phase the element is in",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

            new OA\PathParameter(  name: 'element', description: "The element",
                in: 'path', required: true,  schema: new OA\Schema(ref: '#/components/schemas/HexbatchResource') ),

        ],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\PromoteSet::class)]
    public function promote_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






}
