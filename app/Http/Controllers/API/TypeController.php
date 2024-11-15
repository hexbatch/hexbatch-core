<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\Access\TypeOfAccessMarker;
use App\Helpers\Annotations\ApiAccessMarker;
use App\Helpers\Annotations\ApiEventMarker;
use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;
use OpenApi\Attributes as OA;
use App\Sys\Res\Types\Stk\Root\Evt;




class TypeController extends Controller {


    #[OA\Get(
        path: '/api/v1/types/show',
        operationId: 'core.types.show',
        description: "See information about a type if one is a member, admin or owner ",
        summary: 'Show information about a type',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\Show::class)]
    public function show_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/types/show_public',
        operationId: 'core.types.show_public',
        description: "Anyone can see public information including about and meta, name and current status ",
        summary: 'Show public data for a type',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Type\ShowPublic::class)]
    public function show_type_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/types/list_published',
        operationId: 'core.types.list_published',
        description: "Can see any published types where one is a member, admin or owner ",
        summary: 'List published types',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListPublished::class)]
    public function list_published() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/types/list_suspended',
        operationId: 'core.types.list_suspended',
        description: "Can see any suspended types where one is a member, admin or owner ",
        summary: 'List suspended types',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListSuspended::class)]
    public function list_suspended() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/types/list_all_suspended',
        operationId: 'core.types.list_all_suspended',
        description: "Public can see any suspended types ",
        summary: 'List all suspended types',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Type\ListAllSuspended::class)]
    public function list_all_suspended() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/types/list_live',
        operationId: 'core.types.list_live',
        description: "Members can see all the currently applied live using this type",
        summary: 'Show live types made from this type',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListLive::class)]
    public function list_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Get(
        path: '/api/v1/types/list_elements',
        operationId: 'core.types.list_elements',
        description: "Members can see all the elements created. Use the element show command to get information about element",
        summary: 'Show elements made from this type',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListElements::class)]
    public function list_elements() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Get(
        path: '/api/v1/types/list_descendants',
        operationId: 'core.types.list_descendants',
        description: "Members can see how this type is being inherited by other types.",
        summary: 'Show type inheritance',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListDescendants::class)]
    public function list_descendants() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/types/list_attribute_descendants',
        operationId: 'core.types.list_attribute_descendants',
        description: "Members can see how this type's attributes are being used in other types.",
        summary: 'Show attribute inheritance',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_MEMBER)]
    #[ApiTypeMarker( Root\Api\Type\ListAttributeDescendants::class)]
    public function list_attribute_descendants() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }








    #[OA\Post(
        path: '/api/v1/types/add_handle',
        operationId: 'core.types.add_handle',
        description: "Types can be grouped, organized and controlled together",
        summary: 'Add element handle to a type',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeHandleAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Patch(
        path: '/api/v1/types/remove_handle',
        operationId: 'core.types.remove_handle',
        description: "Handles can be removed at any time, and be empty or new ones added",
        summary: 'Remove element handle from a type',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeHandleRemoved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/types/add_attribute_handle',
        operationId: 'core.types.add_attribute_handle',
        description: "Attributes can be grouped, organized and controlled together by an attribute handle. This can also be used for displaying debugging info",
        summary: 'Add attribute handle to an attribute',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\AttributeHandleAdded::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\AddHandleAttribute::class)]
    public function add_attribute_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/types/remove_attribute_handle',
        operationId: 'core.types.remove_attribute_handle',
        description: "Handles can be removed at any time, and be empty or new ones added",
        summary: 'Remove attribute handle from an attribute',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\AttributeHandleRemoved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\RemoveHandleAttribute::class)]
    public function remove_attribute_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Patch(
        path: '/api/v1/types/change_owner',
        operationId: 'core.types.change_owner',
        description: "The type owner can give the type to any other namespace",
        summary: 'Changes the type owner',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeOwnerChange::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[ApiTypeMarker( Root\Api\Type\ChangeOwner::class)]
    public function change_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/types/promote_owner',
        operationId: 'core.types.promote_owner',
        description: "Type owners can be changed by the system without events or permission",
        summary: 'System can change the type owner',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\PromoteOwner::class)]
    public function promote_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/types/destroy_type',
        operationId: 'core.types.destroy_type',
        description: "The type owner destroy the type, inheritied types and event on this type can block",
        summary: 'Changes the type owner',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeDeleted::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_OWNER)]
    #[ApiTypeMarker( Root\Api\Type\DestroyType::class)]
    public function destroy_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Delete(
        path: '/api/v1/types/purge',
        operationId: 'core.types.purge',
        description: "System can delete types, and all their elements and sets, without events or permission",
        summary: 'System can delete types',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\Purge::class)]
    public function purge_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/types/fire_event',
        operationId: 'core.types.fire_event',
        description: "Custom events can be fired, their scope depends on what they inherit, smallest scope wins if multiple ancestors of mixed scope",
        summary: 'Fires a custom event',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\CustomEventFired::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\FireEvent::class)]
    public function fire_event() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Patch(
        path: '/api/v1/types/publish',
        operationId: 'core.types.publish',
        description: "Type admins do unpublished design and mark it as ready for use. Events from inherited types and attributes can block",
        summary: 'Publishes a design',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypePublished::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\Publish::class)]
    public function publish_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/types/promote_publish',
        operationId: 'core.types.promote_publish',
        description: "System can publish any design. This overrules any rules denying it (those events do not fire) ",
        summary: 'System publishes a design',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\PromotePublish::class)]
    public function publish_type_promote() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/types/suspend',
        operationId: 'core.types.suspend',
        description: "System can suspend a type. New elements cannot be created, but existing ones are left alone. ".
                "\n Suspended types do not listen to events ".
                "\n Can be blocked by events (system only listeners block, others listen to after the fact)",
        summary: 'Suspends a design',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeSuspended::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Type\Suspend::class)]
    public function suspend_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/types/retire',
        operationId: 'core.types.retire',
        description: "Type admins retire a type. Events from inherited types and attributes can block. ".
                    "\nUnpublished types using this after acceptance can still use it",
        summary: 'Retires a design',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\TypeRetired::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]
    #[ApiTypeMarker( Root\Api\Type\Retire::class)]
    public function retire_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



}
