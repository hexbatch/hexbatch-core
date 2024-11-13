<?php

namespace App\Http\Controllers\API;


use App\Helpers\Annotations\Access\TypeOfAccessMarker;
use App\Helpers\Annotations\ApiAccessMarker;
use App\Helpers\Annotations\ApiEventMarker;
use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;

use Symfony\Component\HttpFoundation\Response as CodeOf;
use OpenApi\Attributes as OA;

class DesignController extends Controller {


    #[ApiTypeMarker( Root\Api\Design\ChangeOwner::class)]
    #[ApiEventMarker( Evt\Server\TypeOwnerChange::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[OA\Post(
        path: '/api/v1/design/change_owner',
        operationId: 'core.design.change_owner',
        description: "The owner ns can transfer this unpublished design to be owned by another namespace.".
                    " Owners of subtypes can deny this change by listening to the type_owner_change event raised by this action",
        summary: 'Changes the ownership of a type before its published.  ' ,
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function change_design_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[ApiTypeMarker( Root\Api\Design\PromoteOwner::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[OA\Post(
        path: '/api/v1/design/promote_owner',
        operationId: 'core.design.promote_owner',
        description: 'The system can transfer this design to be managed by another namespace. No events are raised that can deny the change.',
        summary: 'Changes the ownership of a type before its published. ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function promote_design_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[ApiTypeMarker( Root\Api\Design\Promotion::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[OA\Post(
        path: '/api/v1/design/promote',
        operationId: 'core.design.promote',
        description: 'The system can make a new design and assign this to any owner. No events are raised',
        summary: 'Makes a new design type with anyone as the owner ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function promote_design() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[ApiTypeMarker( Root\Api\Design\Destroy::class)]
    #[OA\Delete(
        path: '/api/v1/design/purge',
        operationId: 'core.design.purge',
        description: 'The system can delete any design, owned by anyone, before publishing, without raising any events',
        summary: 'Deletes an unpublished type ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\Purge::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    public function purge_design() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[ApiTypeMarker( Root\Api\Design\Destroy::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[OA\Delete(
        path: '/api/v1/design/destroy',
        operationId: 'core.design.destroy',
        description: 'A namespace can delete a new design, before publishing, without raising any events',
        summary: 'Deletes an unpublished type ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function destroy_design() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[ApiTypeMarker( Root\Api\Design\Create::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    #[OA\Post(
        path: '/api/v1/design/create',
        operationId: 'core.design.create',
        description: 'A namespace can make a new design, they are the owner. No events are raised',
        summary: 'Makes a new design type ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function create_design() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[ApiTypeMarker( Root\Api\Design\Edit::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[OA\Patch(
        path: '/api/v1/design/edit',
        operationId: 'core.design.edit',
        description: "The owner or their admin group can change, before publishing, the name of the type, final status and access level ".
                        "\nNo events are raised",
        summary: 'Edits the name, final type, access ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function edit_design() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[ApiTypeMarker( Root\Api\Design\Show::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[OA\Get(
        path: '/api/v1/design/show',
        operationId: 'core.design.show',
        description: "See details about a type in any state. Lists the attributes, event listeners, live requirements and live rules ".
        "\nIf the design type is set to public access, then any namespace can use this to see the information. Otherwise only the members of the owner namepace can ".
        "\nTo see more detail about any one of these, use the show api for that reference",
        summary: 'Shows information about a type ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function show_design() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[ApiTypeMarker( Root\Api\Design\ListDesigns::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[OA\Get(
        path: '/api/v1/design/list',
        operationId: 'core.design.list',
        description: "See a list of all the designs this namespace either owns or has admin rights to. Once a type is published, it is not seen here".
                "\nCan see the name, uuid, the status and how many attributes, listeners, requirements and rules there are",
        summary: 'Lists all the designed owned or managed  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function list_designs() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/design/show_attribute',
        operationId: 'core.design.show_attribute',
        description: "See information about an attribute. Will list the settings and bounds, will show parent, and status ".
                    "\nShows stats about its descendants".
                    "\nif the type is marked as public, and the attribute is marked as public then any namespace can use this, ".
                    " \notherwise the members of the owning namesapce can ",
        summary: 'Information about a single attribute on a type  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\ShowAttribute::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    public function show_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/design/destroy_attribute',
        operationId: 'core.design.destroy_attribute',
        description: "Destroys an attribute. Attributes cannot be deleted after a type is published ".
        "\nRemoving design attributes does not generate any event",
        summary: 'Delete an attribute',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiTypeMarker( Root\Api\Design\DestroyAttribute::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_OWNER)]
    public function destroy_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/design/promote_attribute',
        operationId: 'core.design.promote_attribute',
        description: "System can create a new attribute using any parent chain for any design owned by anyone ".
        "\nThis does not generate any events",
        summary: 'Creates a new attribute on a design for any design or namespace',
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
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[OA\Post(
        path: '/api/v1/design/create_attribute',
        operationId: 'core.design.create_attribute',
        description: "Namespace admin group can create a new attribute using any parent chain for any design owned by anyone ".
        "\nBut the design can only be published if the inheritance chain does not block this using the design_pending event",
        summary: 'Creates a new attribute on a design',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function create_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/design/list_attributes',
        operationId: 'core.design.list_attributes',
        description: "See a list of all the attributes the type uses".
        "\nShows information about its ancestors".
        "\nif the type is marked as private or the attribute is marked as private,".
        "\n then that attribute is not shown except to the members of the owning namesapce",
        summary: 'Lists the attributes of a type  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Design\ListAttributes::class)]
    public function list_attributes() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Patch(
        path: '/api/v1/design/set_attribute_location',
        operationId: 'core.design.set_attribute_location',
        description: "Each attribute can have one map or shape, or none at all".
        "\nCall this with no boundary to clear".
        "\nThe bounds of the type as a whole is a union of the attribute maps",
        summary: 'Gives the attribute a shape or map  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\AttributeLocation::class)]
    public function set_attribute_location() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/design/test_attribute_location',
        operationId: 'core.design.test_attribute_location',
        description: "Tests the attribute bounds with geojson points and shapes, or other types and attributes".
        "\nif the type is marked as private or the attribute is marked as private,".
        "\n then that attribute is not testable except to the members of the owning namesapce. ".
        "\n If using other attributes to test with, must be able to see that attribute. ",
        summary: 'Allows testing and debugging of an attribute bounds  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Design\AttributeLocationTest::class)]
    public function test_attribute_location() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Patch(
        path: '/api/v1/design/edit_attribute',
        operationId: 'core.design.edit_attribute',
        description: "Owner admin group can set the following properties to an attribute: ".
        "\nparent, boolean properties, merge methods, access, value policy, value rules, initial value",
        summary: 'Edits the properites of an unpublished attribute  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditAttribute::class)]
    public function edit_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/design/create_listener',
        operationId: 'core.design.create_listener',
        description: "Each attribute can have zero or one listeners. If replacing, then earlier must be destroyed. " .
                        "\nOwner admin group can create a listener on each attribute, as long as the type is not published ".
                        "\nIf inheriting from a parent, and that has a listener, then the listener must be for the same event type ",
        summary: 'Makes a new event listener for an attribute on the design ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateListener::class)]
    public function create_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Delete(
        path: '/api/v1/design/destroy_listener',
        operationId: 'core.design.destroy_listener',
        description: "Owner admin group can can remove the event listener from the attribute, before publishing ",
        summary: 'Makes a new event listener for an attribute on the design ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyListener::class)]
    public function destroy_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/design/list_listeners',
        operationId: 'core.design.list_listeners',
        description: "Lists all the listeners, and the attributes that hold them, and the events listened ".
        "\n To see the rules then use the show listener".
        "\n if the type is marked as private or the attribute is marked as private,".
        "\n then the listener is not shown except to the members of the owning namesapce",
        summary: 'Allows testing and debugging of an attribute bounds  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Design\ListListeners::class)]
    public function list_listeners() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/design/show_listener',
        operationId: 'core.design.show_listener',
        description: "Shows the information about the listener, including the rules it uses ".
        "\n This is only shown to the owning namespace members",
        summary: 'Shows information about a specific listener',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Design\ShowListener::class)]
    public function show_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/design/create_rule',
        operationId: 'core.design.create_rule',
        description: "Each listener can have a tree of rules with a single top root, but can have any number of branches or leaves " .
        "\nOwner admin group can add a single rule, or a tree of rules, and that can be attached to the unoccupied root, or to a leaf ",
        summary: 'Creates a rule or rule tree attached to the root or existing rule ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\CreateListenerRule::class)]
    public function create_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/design/destroy_rule',
        operationId: 'core.design.destroy_rule',
        description: "The listener tree can be edited by deleting parts of it. " .
        "\nOwner admin group can prune the tree by branches or leaves ",
        summary: 'Removes a single rule and all its children ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\DestroyListenerRule::class)]
    public function destroy_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/design/edit_rule',
        operationId: 'core.design.edit_rule',
        description: "The listener tree can be edited by changing one rule and its children " .
        "\n For existing rules can change the phase,path,rank,logic, merge method and filter ".
        "\n Owner admin group edit each leaf or branches or the entire tree ",
        summary: 'Edits a single rule and all its children ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\EditListenerRule::class)]
    public function edit_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/design/test_listener',
        operationId: 'core.design.test_listener',
        description: "A listener can be tested against a simulated event " .
        "\n The tester provides the set, element, event, as long as the namespace using this can see the set and/or element, can test ",
        summary: 'Test a rule tree against  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\TestListener::class)]
    public function test_listener() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/design/remove_parent',
        operationId: 'core.design.remove_parent',
        description: "The owner or admin group can remove one or more parents from a design using a path. ".
        "\nThis can be removed regardless of the approval status ".
        "\nParents need approval to use, but do not notify the inheritance chain of design changes when dropping that parent",
        summary: 'Removes a parent',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\RemoveParent::class)]
    public function remove_parent() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/design/add_parent',
        operationId: 'core.design.add_parent',
        description: "The owner or admin group can add one or more published parents using a path.".
        "\n Regardless who owns it, the event of design_pending can block it from the inheritance chaing".
        "\n If a parent is declared retired or suspended before publishing, then this type cannot be published until that is changed".
        "\nParents need approval to use in the design, and later to publish, to look over any conflicts after the design allowed",
        summary: 'Adds a parent',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiEventMarker( Evt\Server\DesignPending::class)]
    #[ApiTypeMarker( Root\Api\Design\AddParent::class)]
    public function add_parent() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/design/list_parents',
        operationId: 'core.design.list_parents',
        description: "Lists the parents the type (published or any other state) uses. ".
        "\nLists the status of each parent, both their own lifecycle and the approval status being used here".
        "\n If the type has public access, then any namespace can see this. Otherwise its members of the owning namespace ",
        summary: 'Lists the parents of a type  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Design\ListParents::class)]
    public function list_parents() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/design/list_live_rules',
        operationId: 'core.design.list_live_rules',
        description: "Lists the live rules defined for this type. ".
        "\n If the type has public access, then any namespace can see this. Otherwise its members of the owning namespace ",
        summary: 'Lists live rules for the type  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Design\ListLiveRules::class)]
    public function list_live_rules() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/design/add_live_rule',
        operationId: 'core.design.add_live_rule',
        description: "Owner admin group can add live rules to be applied after the publishing and making sets out of this " ,
        summary: 'Adds a new live rule to the type before its published ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\AddLiveRule::class)]
    public function add_live_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/design/remove_live_rule',
        operationId: 'core.design.remove_live_rule',
        description: "Owner admin group can remove a live rule before its published " ,
        summary: 'Removes a live rule ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\RemoveLiveRule::class)]
    public function remove_live_rule() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }







    #[OA\Get(
        path: '/api/v1/design/test_location',
        operationId: 'core.design.test_location',
        description: "Tests the bounds of the type with geo-json or given another type, element or set. ".
        "\n If the type is marked as private,".
        "\n then only testable to the members of the owning namesapce. ",
        summary: 'Tests the type schedule  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Design\LocationTest::class)]
    public function test_location() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Get(
        path: '/api/v1/design/test_time',
        operationId: 'core.design.test_time',
        description: "Tests schedule of the type with any generated time string ".
        "\n If the type is marked as private,".
        "\n then only testable to the members of the owning namesapce. ",
        summary: 'Tests the type schedule  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Design\TimeTest::class)]
    public function test_time() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/design/set_time',
        operationId: 'core.design.set_time',
        description: "Sets a schedule for the type before publishing ".
        "\n There is only one schedule, which can be overridden by this, or emptied out by using this with no data",
        summary: 'Sets the schedule for the type  ',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::NAMESPACE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Design\Time::class)]
    public function set_time() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
