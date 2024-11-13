<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class TypeController extends Controller {
    #[ApiTypeMarker( Root\Api\Type\Show::class)]
    public function show_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ShowPublic::class)]
    public function show_type_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ListPublished::class)]
    public function list_published() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ListSuspended::class)]
    public function list_suspended() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ListLive::class)]
    public function list_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ListElements::class)]
    public function list_elements() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ListDescendants::class)]
    public function list_descendants() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ListAttributeDescendants::class)]
    public function list_attribute_descendants() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Type\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\AddHandleAttribute::class)]
    public function add_attribute_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\RemoveHandleAttribute::class)]
    public function remove_attribute_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\ChangeOwner::class)]
    public function change_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\PromoteOwner::class)]
    public function promote_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\DestroyType::class)]
    public function destroy_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\Purge::class)]
    public function purge_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\FireEvent::class)]
    public function fire_event() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\Publish::class)]
    public function publish_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\PromotePublish::class)]
    public function publish_type_promote() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\Suspend::class)]
    public function suspend_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Type\Retire::class)]
    public function retired() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



}
