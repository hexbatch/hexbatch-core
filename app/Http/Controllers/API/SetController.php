<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class SetController extends Controller {
    #[ApiTypeMarker( Root\Api\Set\Show::class)]
    public function show_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\ShowPublic::class)]
    public function show_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\AddElement::class)]
    public function add_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\PromoteMember::class)]
    public function promote_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\AddHandle::class)]
    public function add_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\RemoveHandle::class)]
    public function remove_handle() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\CreateSet::class)]
    public function create_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\DestroySet::class)]
    public function destroy_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\RemoveElement::class)]
    public function remove_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\EmptySet::class)]
    public function empty_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\StickElement::class)]
    public function stick_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\UnstickElement::class)]
    public function unstick_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\ListChildren::class)]
    public function list_children() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\ListMembers::class)]
    public function list_members() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\Purge::class)]
    public function purge_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\PurgeMember::class)]
    public function purge_member() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Set\PromoteSet::class)]
    public function promote_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

}

