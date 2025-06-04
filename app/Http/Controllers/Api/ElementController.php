<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class ElementController extends Controller {

    #[OA\Patch(
        path: '/api/v1/{namespace}/elements/change_owner',
        operationId: 'core.elements.change_owner',
        description: "Element owner can give ownership to another namespace at any time. Any number of elements can be included with a path ",
        summary: 'Change the element owner',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementOwnerChange::class)]
    #[ApiEventMarker( Evt\Type\ElementRecieved::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_OWNER)]
    #[ApiTypeMarker( Root\Api\Element\ChangeOwner::class)]
    public function change_owner() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }







    #[OA\Patch(
        path: '/api/v1/{namespace}/elements/type_off',
        operationId: 'core.elements.type_off',
        description: "Element admin group turn off attributes in groups of subtype (parent types) in elements inside sets given by a path ",
        summary: 'Turn off all the subtype attributes of elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementTypeTurningOff::class)]
    #[ApiEventMarker( Evt\Set\ElementTypeTurnedOff::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\TypeOff::class)]
    public function type_off() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/{namespace}/elements/type_on',
        operationId: 'core.elements.type_on',
        description: "Element admin group turn on all parent type attributes. The types, elements and sets given by a path ",
        summary: 'Turn on all the attributes of a parent type in elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Set\ElementTypeTurningOn::class)]
    #[ApiEventMarker( Evt\Set\ElementTypeTurnedOn::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\TypeOn::class)]
    public function type_on() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{namespace}/elements/read_attribute',
        operationId: 'core.elements.read_attribute',
        description: "Can select the same attribute(s) in elements(s) to read, ".
                    "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read the same attributes in one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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


    #[OA\Get(
        path: '/api/v1/{namespace}/elements/read_live_type',
        operationId: 'core.elements.read_live_type',
        description: "This is the same as core.elements.read_type but looks at the live and ignores the inherited",
        summary: 'Read all the attributes of a type in one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/{namespace}/elements/read_type',
        operationId: 'core.elements.read_type',
        description:  "Can select the same type(s) in elements(s) to read, will either succeed if can read all of them or fail if one cannot be read ".
            "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read all the attributes of a live type in one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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









    #[OA\Patch(
        path: '/api/v1/{namespace}/elements/write_attribute',
        operationId: 'core.elements.write_attribute',
        description: "Write one or more elements found in a path, that have the same attributes. If one can. ",
        summary: 'Write json to the same attributes of one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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




    #[OA\Get(
        path: '/api/v1/{namespace}/elements/read_time',
        operationId: 'core.elements.read_time',
        description: "Can read current or next time span of the element's type, its parents and applied live ".
        "\n its up to the attribute access, the type access and the event handlers to decide who can ",
        summary: 'Read the locations of one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/{namespace}/elements/add_live',
        operationId: 'core.elements.add_live',
        description: "If can read any attribute on a type, can add it as a live part to one or more elements in a search path.".
                "\n If not part of the element ns, can still add it if part of the type ns and event listeners allow  ",
        summary: 'Add a live type to one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/{namespace}/elements/copy_live',
        operationId: 'core.elements.copy_live',
        description: "If can read any attribute on both the source and destination type, can copy its and its state to a new element.".
        "\n If not part of the element ns, can still add it if part of the type ns and event listeners allow  ",
        summary: 'Copy a live type and its state to one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/{namespace}/elements/remove_live',
        operationId: 'core.elements.remove_live',
        description: "If can read any attribute on the live type, can remove its and its state to a new element.".
        "\n If not part of the element ns, can still remove it if part of the type ns and event listeners allow  ",
        summary: 'Remove a live type from one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/{namespace}/elements/ping',
        operationId: 'core.elements.ping',
        description: "Element ns can use that to ping elements to target sets.",
        summary: 'Ping one or more elements to a set',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_MEMBER)]
    #[ApiTypeMarker( Root\Api\Element\Ping::class)]
    public function ping_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }








    #[OA\Delete(
        path: '/api/v1/{namespace}/elements/destroy',
        operationId: 'core.elements.destroy_element',
        description: "Element admin can destroy one or more elements, the type or parent types can reject this",
        summary: 'Destroys one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Type\ElementDestruction::class)]
    #[ApiEventMarker( Evt\Type\ElementDestroyed::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\Destroy::class)]
    public function destroy_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/elements/purge',
        operationId: 'core.elements.purge_element',
        description: "System can destroy one or more elements without permission",
        summary: 'System Destroy one or more elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/{namespace}/elements/link',
        operationId: 'core.elements.link',
        description: "Anyone can make a link from an element they administer to a target set, or sets. The element does not have to belong to the set ".
        "\n The link can be assigned to another namespace, they can reject that. The linked set can reject the link",
        summary: 'Makes a link between an element and a set',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\LinkCreated::class)]
    #[ApiEventMarker( Evt\Server\LinkCreating::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\Link::class)]
    public function create_link() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }






    #[OA\Get(
        path: '/api/v1/{namespace}/elements/show',
        operationId: 'core.elements.show_element',
        description: "Element members can see details about an element",
        summary: 'Shows the value and information about an element',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/{namespace}/elements/list',
        operationId: 'core.elements.list_elements',
        description: "Element members can see a list of all the elements of namespaces they belong",
        summary: 'Shows list of elements',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
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
        path: '/api/v1/elements/public',
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

    #[OA\Post(
        path: '/api/v1/{namespace}/elements/create_set',
        operationId: 'core.elements.create_set',
        description: "Element namespace admins can create sets out of those elements. Inheritied types can deny. Sets can be created a children of other sets",
        summary: 'Create a set from element',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Server\SetCreated::class)]
    #[ApiEventMarker( Evt\Set\SetChildCreated::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::ELEMENT_ADMIN)]
    #[ApiTypeMarker( Root\Api\Element\CreateSet::class)]
    public function create_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elements/promote_set',
        operationId: 'core.elements.promote_set',
        description: "System can make sets from any element without permisision. No events ",
        summary: 'System can make sets',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\PromoteSet::class)]
    public function promote_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elements/promote_live',
        operationId: 'core.elements.promote_live',
        description: "System can add live types without permisision. No events ",
        summary: 'System adds live types',
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
        path: '/api/v1/{namespace}/elements/promote_live',
        operationId: 'core.elements.promote_live',
        description: "System can remove live types from elements without permisision. No events ",
        summary: 'System subtracts live types',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Element\DemoteLive::class)]
    public function demote_live() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
