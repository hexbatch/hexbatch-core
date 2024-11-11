<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class ElementController extends Controller {
    #[ApiTypeMarker( Root\Api\Element\ChangeOwner::class)]
    public function change_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[ApiTypeMarker( Root\Api\Element\AttributeOff::class)]
    public function element_attribute_off() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\AttributeOn::class)]
    public function element_attribute_on() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\TypeOff::class)]
    public function type_off() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\TypeOn::class)]
    public function type_on() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\ReadAttribute::class)]
    public function read_element_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\Location::class)]
    public function read_element_location() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\Time::class)]
    public function read_element_time() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Element\WriteAttribute::class)]
    public function write_element_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\WriteVisual::class)]
    public function write_visual() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\AddLive::class)]
    public function add_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\Ping::class)]
    public function ping_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\CopyLive::class)]
    public function copy_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\RemoveLive::class)]
    public function remove_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\Create::class)]
    public function create_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\Destroy::class)]
    public function destroy_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Element\Promote::class)]
    public function promote_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\PromoteEdit::class)]
    public function promote_edit_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\Purge::class)]
    public function purge_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[ApiTypeMarker( Root\Api\Element\Link::class)]
    public function link() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\UnLink::class)]
    public function unlink() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\ListLinks::class)]
    public function list_links() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\ShowLink::class)]
    public function show_link() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[ApiTypeMarker( Root\Api\Element\Show::class)]
    public function show_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\ListElements::class)]
    public function list_elements() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Element\ShowPublic::class)]
    public function show_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
