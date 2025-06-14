<?php

namespace App\Http\Controllers\Api;


use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Models\ElementType;
use App\OpenApi\Callbacks\HexbatchCallbackCollectionResponse;
use App\OpenApi\Params\Design\DesignDestroyParams;
use App\OpenApi\Params\Design\DesignOwnershipParams;
use App\OpenApi\Params\Design\DesignParams;
use App\OpenApi\Params\Design\DesignParentParams;
use App\OpenApi\Resources\HexbatchAttribute;
use App\OpenApi\Resources\HexbatchNamespace;
use App\OpenApi\Resources\HexbatchResource;
use App\OpenApi\Types\TypeResponse;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Api;
use App\Sys\Res\Types\Stk\Root\Evt;
use Hexbatch\Things\OpenApi\Things\ThingResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class DesignController extends Controller {


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\ChangeOwner::class)]
    #[ApiEventMarker( Evt\Server\TypeOwnerChanging::class)]
    #[ApiEventMarker( Evt\Server\TypeOwnerChanged::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/change_owner',
        operationId: 'core.design.change_owner',
        description: "The owner ns can transfer this unpublished design to be owned by another namespace.".
                    " Owners of subtypes can deny this change by listening to the type_owner_change event raised by this action",
        summary: 'Changes the ownership of a type before its published.  ' ,
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Ownership changed', content: new JsonContent(ref: TypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function change_design_owner(Request $request,ElementType $type) {
        $params = new DesignOwnershipParams(type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\ChangeOwner(params: $params, is_async: false, tags: ['change-owner-by-web','api-top']); //todo change to true
        $thing = $api->createThingTree(tags: ['change-owner']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\PromoteOwner::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/promote_owner',
        operationId: 'core.design.promote_owner',
        description: 'The system can transfer this design to be managed by another namespace. No events are raised that can deny the change.',
        summary: 'Changes the ownership of a type before its published. ',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Ownership changed', content: new JsonContent(ref: TypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function promote_design_owner(Request $request,ElementType $type) {
        $params = new DesignOwnershipParams(type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\PromoteOwner(params: $params, is_async: true, tags: ['promote-owner-by-web','api-top']);
        $thing = $api->createThingTree(tags: ['promote-owner']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Purge::class)]
    #[OA\Delete(
        path: '/api/v1/{namespace}/design/{element_type}/purge',
        operationId: 'core.design.purge',
        description: 'The system can delete any design, owned by anyone, before publishing, without raising any events',
        summary: 'Purges an unpublished type ',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignDestroyParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Design purged', content: new JsonContent(ref: TypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\Purge::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    public function purge_design(Request $request,ElementType $type) {
        $params = new DesignDestroyParams(destroy_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Purge(params: $params, is_async: false, tags: ['purge-design-by-web','api-top']); //todo change to true
        $thing = $api->createThingTree(tags: ['change-owner']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Destroy::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[OA\Delete(
        path: '/api/v1/{namespace}/design/{element_type}/destroy',
        operationId: 'core.design.destroy',
        description: 'A namespace can delete a new design, before publishing, without raising any events',
        summary: 'Deletes an unpublished type ',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignDestroyParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Design destroyed', content: new JsonContent(ref: TypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function destroy_design(Request $request,ElementType $type) {
        $params = new DesignDestroyParams(destroy_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Destroy(params: $params, is_async: false, tags: ['destroy-design-by-web','api-top']); //todo change to true
        $thing = $api->createThingTree(tags: ['change-owner']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Create::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[OA\Post(
        path: '/api/v1/{namespace}/design/create',
        operationId: 'core.design.create',
        description: 'A namespace can make a new design, they are the owner. No events are raised',
        summary: 'Makes a new design type ',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_CREATED, description: 'Type created', content: new JsonContent(ref: TypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    public function create_design(Request $request) {
        $params = new DesignParams(namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Create(params: $params, is_async: true, tags: ['registration-by-web','api-top']);
        $thing = $api->createThingTree(tags: ['create-design']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[ApiTypeMarker( Root\Api\Design\Edit::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[OA\Patch(
        path: '/api/v1/{namespace}/design/{element_type}/edit',
        operationId: 'core.design.edit',
        description: "The owner or their admin group can change, before publishing, the name of the type, final status and access level ".
                        "\nNo events are raised",
        summary: 'Edits the name, final type, access ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class ),],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function edit_design(Request $request,ElementType $type) {
        $params = new DesignParams(edit_type: $type, namespace: Utilities::getCurrentOrUserNamespace());
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\Edit(params: $params, is_async: true, tags: ['edit-design-by-web','api-top']);
        $thing = $api->createThingTree(tags: ['edit-design']);
        Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }





    #[ApiTypeMarker( Root\Api\Design\Show::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/show',
        operationId: 'core.design.show',
        description: "See details about a type in any state. Lists the attributes, event listeners, live requirements and live rules ".
        "\nIf the design type is set to public access, then any namespace can use this to see the information. Otherwise only the members of the owner namepace can ".
        "\nTo see more detail about any one of these, use the show api for that reference",
        summary: 'Shows information about a type ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function show_design() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[ApiTypeMarker( Root\Api\Design\ListDesigns::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[OA\Get(
        path: '/api/v1/{namespace}/design/list',
        operationId: 'core.design.list',
        description: "See a list of all the designs this namespace either owns or has admin rights, is a member. Once a type is published, it is not seen here".
                "\nCan see the name, uuid, the status and how many attributes, listeners, requirements and rules there are",
        summary: 'Lists all the designed owned or managed  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function list_designs() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/show',
        operationId: 'core.design.show_attribute',
        description: "See information about an attribute. Will list the settings and bounds, will show parent, and status ".
                    "\nShows stats about its descendants".
                    "\nif the type is marked as public, and the attribute is marked as public then any namespace can use this, ".
                    " \notherwise the members of the owning namesapce can ",
        summary: 'Information about a single attribute on a type  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class),
            new OA\PathParameter(  ref: HexbatchResource::class ),new OA\PathParameter(  ref: HexbatchAttribute::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\ShowAttribute::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    public function show_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/destroy',
        operationId: 'core.design.destroy_attribute',
        description: "Destroys an attribute. Attributes cannot be deleted after a type is published ".
        "\nRemoving design attributes does not generate any event",
        summary: 'Delete an attribute',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\DestroyAttribute::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    public function destroy_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/promote',
        operationId: 'core.design.promote_attribute',
        description: "System can create a new attribute using any parent chain for any design owned by anyone ".
        "\nThis does not generate any events",
        summary: 'Creates a new attribute on a design for any design or namespace',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\AttributePromotion::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    public function promote_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[ApiTypeMarker( Root\Api\Design\CreateAttribute::class)]
    #[ApiEventMarker( Evt\Server\DesignPending::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/create_attribute',
        operationId: 'core.design.create_attribute',
        description: "Namespace admin group can create a new attribute using any parent chain for any design owned by anyone ".
        "\nBut the design can only be published if the inheritance chain does not block this using the design_pending event",
        summary: 'Creates a new attribute on a design',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function create_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/list_attributes',
        operationId: 'core.design.list_attributes',
        description: "See a list of all the attributes the type uses".
        "\nShows information about its ancestors".
        "\nif the type is marked as private or the attribute is marked as private,".
        "\n then that attribute is not shown except to the members of the owning namesapce",
        summary: 'Lists the attributes of a type  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ListAttributes::class)]
    public function list_attributes() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Patch(
        path: '/api/v1/{namespace}/design/list_locations',
        operationId: 'core.design.list_locatations',
        description: "Lists locations",
        summary: 'Lists locations  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\ListLocations::class)]
    public function list_locatations() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{namespace}/design/list_times',
        operationId: 'core.design.list_times',
        description: "Lists times",
        summary: 'Lists times  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\ListTimes::class)]
    public function list_times() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }










    #[OA\Patch(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/edit',
        operationId: 'core.design.edit_attribute',
        description: "Owner admin group can set the following properties to an attribute: ".
        "\nparent, boolean properties, merge methods, access, value policy, value rules, initial value",
        summary: 'Edits the properites of an unpublished attribute  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditAttribute::class)]
    public function edit_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/create_listener',
        operationId: 'core.design.create_listener',
        description: "Each attribute can have zero or one listeners. If replacing, then earlier must be destroyed. " .
                        "\nOwner admin group can create a listener on each attribute, as long as the type is not published ".
                        "\nIf inheriting from a parent, and that has a listener, then the listener must be for the same event type ",
        summary: 'Makes a new event listener for an attribute on the design ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateListener::class)]
    public function create_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Delete(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/destroy_listener',
        operationId: 'core.design.destroy_listener',
        description: "Owner admin group can can remove the event listener from the attribute, before publishing ",
        summary: 'Makes a new event listener for an attribute on the design ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyListener::class)]
    public function destroy_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/list_listeners',
        operationId: 'core.design.list_listeners',
        description: "Lists all the listeners, and the attributes that hold them, and the events listened ".
        "\n To see the rules then use the show listener. Any member can see",
        summary: 'Allows testing and debugging of an attribute bounds  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ListListeners::class)]
    public function list_listeners() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/show_listener',
        operationId: 'core.design.show_listener',
        description: "Shows the information about the listener, including the rules it uses ".
        "\n This is only shown to the owning namespace members",
        summary: 'Shows information about a specific listener',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ShowListener::class)]
    public function show_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/create_rule',
        operationId: 'core.design.create_rule',
        description: "Each listener can have a tree of rules with a single top root, but can have any number of branches or leaves " .
        "\nOwner admin group can add a single rule, or a tree of rules, and that can be attached to the unoccupied root, or to a leaf ",
        summary: 'Creates a rule or rule tree attached to the root or existing rule ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateListenerRule::class)]
    public function create_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/design/{element_type}/attribute/{attribute}/rule/{attribute_rule}/destroy_rule',
        operationId: 'core.design.destroy_rule',
        description: "The listener tree can be edited by deleting parts of it. " .
        "\nOwner admin group can prune the tree by branches or leaves ",
        summary: 'Removes a single rule and all its children ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyListenerRule::class)]
    public function destroy_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/rule/{attribute_rule}/edit_rule',
        operationId: 'core.design.edit_rule',
        description: "The listener tree can be edited by changing one rule and its children " .
        "\n For existing rules can change the phase,path,rank,logic, merge method and filter ".
        "\n Owner admin group edit each leaf or branches or the entire tree ",
        summary: 'Edits a single rule and all its children ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditListenerRule::class)]
    public function edit_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/rule/{attribute_rule}/test_rule',
        operationId: 'core.design.test_rule',
        description: "Can test part of the listener tree to figure out bugs ",
        summary: 'Test a rule (or a rule tree) ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditListenerRule::class)]
    public function test_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/test_listener',
        operationId: 'core.design.test_listener',
        description: "A listener can be tested against a simulated event " .
        "\n The tester provides the set, element, event, as long as the namespace using this can see the set and/or element, can test ",
        summary: 'Test a rule tree against  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\TestListener::class)]
    public function test_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    /**
     * @throws \Exception
     */
    #[OA\Delete(
        path: '/api/v1/{namespace}/design/{element_type}/remove_parent',
        operationId: 'core.design.remove_parent',
        description: "The owner or admin group can remove one or more parents from a design using a path. ".
        "\nThis can be removed regardless of the approval status ".
        "\nParents need approval to use, but do not notify the inheritance chain of design changes when dropping that parent",
        summary: 'Removes a parent',

        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParentParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Parents removed', content: new JsonContent(ref: TypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\RemoveParent::class)]
    public function remove_parent(Request $request,ElementType $type) {
        $params = new DesignParentParams(given_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\AddParent(params: $params, is_async: false, tags: ['add-parent-by-web','api-top']); //todo change to true
        $thing = $api->createThingTree(tags: ['change-owner']); Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }


    /**
     * @throws \Exception
     */
    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/add_parent',
        operationId: 'core.design.add_parent',
        description: "The owner or admin group can add one or more published parents using a path.".
        "\n Regardless who owns it, the event of design_pending can block it from the inheritance chaing".
        "\n If a parent is declared retired or suspended before publishing, then this type cannot be published until that is changed".
        "\nParents need approval to use in the design, and later to publish, to look over any conflicts after the design allowed",
        summary: 'Adds a parent',
        requestBody: new OA\RequestBody( required: true, content: new JsonContent(type: DesignParentParams::class)),
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response(    response: CodeOf::HTTP_ACCEPTED, description: 'Parents added', content: new JsonContent(ref: TypeResponse::class)),
            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Thing is processing|waiting',
                content: new JsonContent(ref: ThingResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_OK, description: 'Success but other callbacks',
                content: new JsonContent(ref: HexbatchCallbackCollectionResponse::class)),

            new OA\Response(    response: CodeOf::HTTP_BAD_REQUEST, description: 'There was an issue',
                content: new JsonContent(ref: ThingResponse::class))
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiEventMarker( Evt\Server\DesignPending::class)]
    #[ApiTypeMarker( Root\Api\Design\AddParent::class)]
    public function add_parent(Request $request,ElementType $type) {
        $params = new DesignParentParams(given_type: $type);
        $params->fromCollection(new Collection($request->all()));
        $api = new Api\Design\RemoveParent(params: $params, is_async: false, tags: ['remove-parent-by-web','api-top']); //todo change to true
        $thing = $api->createThingTree(tags: ['change-owner']); Utilities::ignoreVar($thing);
        $data_out = $api->getCallbackResponse($http_code);
        return  response()->json(['response'=>$data_out],$http_code);
    }




    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/list_parents',
        operationId: 'core.design.list_parents',
        description: "Lists the parents the type (published or any other state) uses. ".
        "\nLists the status of each parent, both their own lifecycle and the approval status being used here".
        "\n If the type has public access, then any namespace can see this. Otherwise its members of the owning namespace ",
        summary: 'Lists the parents of a type  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ListParents::class)]
    public function list_parents() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/{namespace}/design/{element_type}/list_live_rules',
        operationId: 'core.design.list_live_rules',
        description: "Lists the live rules defined for this type. ".
        "\n If the type has public access, then any namespace can see this. Otherwise its members of the owning namespace ",
        summary: 'Lists live rules for the type  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ListLiveRules::class)]
    public function list_live_rules() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/design/{element_type}/add_live_rule',
        operationId: 'core.design.add_live_rule',
        description: "Owner admin group can add live rules to be applied after the publishing and making sets out of this " ,
        summary: 'Adds a new live rule to the type before its published ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\AddLiveRule::class)]
    public function add_live_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/design/{element_type}/remove_live_rule',
        operationId: 'core.design.remove_live_rule',
        description: "Owner admin group can remove a live rule before its published " ,
        summary: 'Removes a live rule ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\RemoveLiveRule::class)]
    public function remove_live_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/design/time/{time_bound}/destroy',
        operationId: 'core.design.destroy_time',
        description: "Destroys a time resource, but only if its not used. ",
        summary: 'Remove a time resource  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\DestroyTime::class)]
    public function destroy_time() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/design/location_create',
        operationId: 'core.design.location_create',
        description: "Makes a new geo json 2d or 3d shape",
        summary: 'Makes a new location bound',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\CreateLocation::class)]
    public function location_create() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Patch(
        path: '/api/v1/{namespace}/design/location/{location_bound}/edit',
        operationId: 'core.design.location_edit',
        description: "Change visual properties of a location",
        summary: 'Makes a new location bound',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\EditLocation::class)]
    public function location_edit() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Delete(
        path: '/api/v1/{namespace}/design/location/{location_bound}/destroy',
        operationId: 'core.design.destroy_location',
        description: "Destorys a location resource if its not used ",
        summary: 'Destroy a location  ',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\DestroyLocation::class)]
    public function destroy_location() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/design/create_time',
        operationId: 'core.design.create_time',
        description: "Makes a schedule that can be used in one or more attributes",
        summary: 'Makes a new schedule',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateTime::class)]
    public function create_time() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Patch(
        path: '/api/v1/{namespace}/design/time/{time_bound}/edit',
        operationId: 'core.design.time_edit',
        description: "Schedules can be changed",
        summary: 'Edits a schedule',
        parameters: [new OA\PathParameter(  ref: HexbatchNamespace::class ),new OA\PathParameter(  ref: HexbatchResource::class )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\EditTime::class)]
    public function time_edit() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
