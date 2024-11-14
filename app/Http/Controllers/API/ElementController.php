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

class ElementController extends Controller {

    #[OA\Patch(
        path: '/api/v1/elements/change_owner',
        operationId: 'core.elements.change_owner',
        description: "Element owner can give ownership to another namespace at any time. Any number of elements can be included with a path ",
        summary: 'Change the element owner',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementOwnerChange::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_OWNER)]
    #[ApiTypeMarker( Root\Api\Element\ChangeOwner::class)]
    public function change_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Patch(
        path: '/api/v1/elements/attribute_off',
        operationId: 'core.elements.attribute_off',
        description: "Element admin group turn off attributes of elements in sets given by a path ",
        summary: 'Turn off an attribute of elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementAttributeOff::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\AttributeOff::class)]
    public function attribute_off() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Patch(
        path: '/api/v1/elements/attribute_on',
        operationId: 'core.elements.attribute_on',
        description: "Element admin group turn on attributes of elements in sets given by a path ",
        summary: 'Turn on an attribute of elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementAttributeOn::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\AttributeOn::class)]
    public function attribute_on() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Patch(
        path: '/api/v1/elements/type_off',
        operationId: 'core.elements.type_off',
        description: "Element admin group turn off attributes in groups of subtype (parent types) in elements inside sets given by a path ",
        summary: 'Turn off all the subtype attributes of elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementTypeOff::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\TypeOff::class)]
    public function type_off() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/elements/type_on',
        operationId: 'core.elements.type_on',
        description: "Element admin group turn on all parent type attributes. The types, elements and sets given by a path ",
        summary: 'Turn on all the attributes of a parent type in elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementTypeOn::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\TypeOn::class)]
    public function type_on() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/elements/read_attribute',
        operationId: 'core.elements.read_attribute',
        description: "Can select the same attribute(s) in elements(s) to read, ".
                    "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read the same attributes in one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\Reading::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Element\ReadAttribute::class)]
    public function read_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }









    #[OA\Patch(
        path: '/api/v1/elements/write_attribute',
        operationId: 'core.elements.write_attribute',
        description: "Write one or more elements found in a path, that have the same attributes. If one can. ",
        summary: 'Write json to the same attributes of one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\AttributeWrite::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiTypeMarker( Root\Api\Element\WriteAttribute::class)]
    public function write_attribute() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }







    #[OA\Patch(
        path: '/api/v1/elements/write_visual',
        operationId: 'core.elements.write_visual',
        description: "Admin groups changes visual rendering of opacity|color|border|texture for one or more elements found in a path, ".
        "\n that have the same attributes. The system here does not render, so its up to the api clients to do something with the settings ",
        summary: 'Write opacity|color|border|texture to the same attributes of one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Element\WritingVisual::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\WriteVisual::class)]
    public function write_visual() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/elements/read_visual',
        operationId: 'core.elements.read_visual',
        description: "Can read pathed elements' geometry and maps due to design and live types ".
        "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read the locations of one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiAccessMarker( TypeOfAccessMarker::MIXED)]
    #[ApiEventMarker( Evt\Element\ReadingVisual::class)]
    #[ApiTypeMarker( Root\Api\Element\ReadVisual::class)]
    public function read_visual() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/elements/read_time',
        operationId: 'core.elements.read_time',
        description: "Can read current or next time span of the element's type, its parents and applied live ".
        "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read the locations of one or more elements',
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
        path: '/api/v1/elements/add_live',
        operationId: 'core.elements.add_live',
        description: "If can read any attribute on a type, can add it as a live part to one or more elements in a search path.".
                "\n If not part of the element ns, can still add it if part of the type ns and event listeners allow  ",
        summary: 'Add a live type to one or more elements',
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
        path: '/api/v1/elements/copy_live',
        operationId: 'core.elements.copy_live',
        description: "If can read any attribute on both the source and destination type, can copy its and its state to a new element.".
        "\n If not part of the element ns, can still add it if part of the type ns and event listeners allow  ",
        summary: 'Copy a live type and its state to one or more elements',
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
        path: '/api/v1/elements/remove_live',
        operationId: 'core.elements.remove_live',
        description: "If can read any attribute on the live type, can remove its and its state to a new element.".
        "\n If not part of the element ns, can still remove it if part of the type ns and event listeners allow  ",
        summary: 'Remove a live type from one or more elements',
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
        path: '/api/v1/elements/ping',
        operationId: 'core.elements.ping_element',
        description: "Element ns can use that to ping elements to target sets.",
        summary: 'Ping one or more elements to a set',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\Ping::class)]
    public function ping_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/elements/create',
        operationId: 'core.elements.create_element',
        description: "Type admin can create one or more elements going to one or more namespaces. The namespace can reject. The inherited types can reject",
        summary: 'Creates one or more new elements from a type',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementOwnerChange::class)]
    #[ApiEventMarker( Evt\Element\ElementRecieved::class)]
    #[ApiEventMarker( Evt\Element\ElementRecievedBatch::class)]
    #[ApiEventMarker( Evt\Type\ElementOwnerChangeBatch::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::TYPE_ADMIN)]

    #[ApiTypeMarker( Root\Api\Element\Create::class)]
    public function create_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Delete(
        path: '/api/v1/elements/destroy',
        operationId: 'core.elements.destroy_element',
        description: "Element admin can destroy one or more elements, the type or parent types can reject this",
        summary: 'Destroys one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementDestruction::class)]
    #[ApiEventMarker( Evt\Type\ElementDestructionBatch::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\Destroy::class)]
    public function destroy_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/elements/promote',
        operationId: 'core.elements.promote_element',
        description: "System can make elements out of any types, not needing permissions. No events created when doing this",
        summary: 'Creates one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\Promote::class)]
    public function promote_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Patch(
        path: '/api/v1/elements/promote_edit',
        operationId: 'core.elements.promote_edit_element',
        description: "System can update elements not needing permissions. No events created when doing this",
        summary: 'Changes owner or value of or phase of one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\PromoteEdit::class)]
    public function promote_edit_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Delete(
        path: '/api/v1/elements/purge',
        operationId: 'core.elements.purge_element',
        description: "System can destroy one or more elements without permission",
        summary: 'System Destroy one or more elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\Purge::class)]
    public function purge_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/elements/link',
        operationId: 'core.elements.link',
        description: "Anyone can make a link from an element they administer to a target set, or sets. The element does not have to belong to the set ".
        "\n The link can be assigned to another namespace, they can reject that. The linked set can reject the link",
        summary: 'Makes a link between an element and a set',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\LinkCreated::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\Link::class)]
    public function link() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/elements/unlink',
        operationId: 'core.elements.unlink',
        description: "Link admin can remove one or more links they control",
        summary: 'Destroys one or more links',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\LinkDestroyed::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\UnLink::class)]
    public function unlink() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/elements/list_links',
        operationId: 'core.elements.list_links',
        description: "Link members can see all the links owned by namespaces they belong",
        summary: 'Shows a list of links',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ListLinks::class)]
    public function list_links() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/elements/show_link',
        operationId: 'core.elements.show_link',
        description: "Link member can see information about a particular link",
        summary: 'Show a link',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::LINK_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ShowLink::class)]
    public function show_link() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/elements/show',
        operationId: 'core.elements.show_element',
        description: "Element members can see details about an element",
        summary: 'Shows the value and information about an element',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\Show::class)]
    public function show_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/elements/list',
        operationId: 'core.elements.list_elements',
        description: "Element members can see a list of all the elements of namespaces they belong",
        summary: 'Shows list of elements',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\ListElements::class)]
    public function list_elements() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/elements/show_public',
        operationId: 'core.elements.show_public',
        description: "Anyone can see public information about an element, attributes that are marked as public will show their data.",
        summary: 'Shows public information about an element',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Element\ShowPublic::class)]
    public function show_public() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
