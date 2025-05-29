<?php

namespace App\Http\Controllers\Api;

use App\Annotations\Access\TypeOfAccessMarker;
use App\Annotations\ApiAccessMarker;
use App\Annotations\ApiEventMarker;
use App\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use App\Sys\Res\Types\Stk\Root\Evt;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;


class ElsewhereController extends Controller {


    #[OA\Post(
        path: '/api/v1/elsewhere/register',
        operationId: 'core.elsewhere.register',
        description: "Any server can register, they are sent their credentials and user info to their api . Registration can be blocked by event",
        summary: 'Servers can register here',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ServerRegistered::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Elsewhere\Register::class)]
    public function register_elsewhere() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/elsewhere/ask_credentials',
        operationId: 'core.elsewhere.ask_credentials',
        description: "Any server already registered can ask for new credentials and it will be sent to their api . This can be blocked by event",
        summary: 'Servers can renew credentials',
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereCredentialsAsking::class)]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereCredentialsSending::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Elsewhere\AskCredentials::class)]
    public function ask_credentials() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/give_credentials',
        operationId: 'core.elsewhere.give_credentials',
        description: "The elsewhere gave us credentials to use on their server. This will replace the older ones",
        summary: 'Servers can renew credentials',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereCredentialsNew::class)]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereCredentialsBad::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\GiveCredentials::class)]
    public function give_credentials() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/push_credentials',
        operationId: 'core.elsewhere.push_credentials',
        description: "Can send new credentials to regisered server . This can be blocked by event",
        summary: 'Servers can have credentials renewed without them asking',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereCredentialsSending::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\PushCredentials::class)]
    public function push_credentials() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/{namespace}/elsewhere/change_status',
        operationId: 'core.elsewhere.change_status',
        description: "Can change the server status to allowed|blocked|paused|pending. Events can block",
        summary: 'System can change server status',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ServerStatusAllowed::class)]
    #[ApiEventMarker( Evt\Elsewhere\ServerStatusBlocked::class)]
    #[ApiEventMarker( Evt\Elsewhere\ServerStatusPaused::class)]
    #[ApiEventMarker( Evt\Elsewhere\ServerStatusPending::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\ChangeStatus::class)]
    public function change_status() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/give_namespace',
        operationId: 'core.elsewhere.give_namespace',
        description: "Servers can combine many namespaces at once to avoid extra http calls. Events can filter or block this",
        summary: 'A new namespace is given from elsewhere',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereGivesNamespace::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\GiveNamespace::class)]
    public function give_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/give_set',
        operationId: 'core.elsewhere.give_set',
        description: "Servers can combine many sets at once to avoid extra http calls. Events can filter or block this",
        summary: 'A new set is given from elsewhere',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereGivesSet::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\GiveSet::class)]
    public function give_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/give_type',
        operationId: 'core.elsewhere.give_type',
        description: "Servers can combine many types at once to avoid extra http calls. Events can filter or block this",
        summary: 'A new type is given from elsewhere',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereGivesType::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\GiveType::class)]
    public function give_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/give_event',
        operationId: 'core.elsewhere.give_event',
        description: "The event runs if the type has setup the listener to be run by that server. Can be blocked before the listener is run",
        summary: 'An event to run is given from elsewhere',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereGivesEvent::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\GiveEvent::class)]
    public function give_event() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/give_element',
        operationId: 'core.elsewhere.give_element',
        description: "Servers can combine many element at once to avoid extra http calls. Events can filter or block this ".
        "\n Elements that have the same guid and type as here are considered re-entered",
        summary: 'A new element is given from elsewhere',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereGivesElement::class)]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereElementReentered::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\GiveElement::class)]
    public function give_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/share_element',
        operationId: 'core.elsewhere.share_element',
        description: "Server A wants to give Server B the element that we gave them earlier. ".
        "\n The response here will give B that element unless blocked ",
        summary: 'Server wants to share element we gave them',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereSharingElement::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\ShareElement::class)]
    public function share_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/ask_element',
        operationId: 'core.elsewhere.ask_element',
        description: "System can initiate transfer for elements. Can be many in one request. ".
                "\nOnce the elements are talked about, will ask for the other stuff related, if missing. ".
                "\nEvents can block this request ",
        summary: 'Try to get element from another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereAskingElement::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\AskElement::class)]
    public function ask_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/ask_type',
        operationId: 'core.elsewhere.ask_type',
        description: "System can initiate transfer for types. Can be many in one request ",
        summary: 'Try to get type from another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereAskingType::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\AskType::class)]
    public function ask_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/ask_set',
        operationId: 'core.elsewhere.ask_set',
        description: "System can initiate transfer for sets. Can be many in one request ",
        summary: 'Try to get set from another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereAskingSet::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\AskSet::class)]
    public function ask_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/ask_namespace',
        operationId: 'core.elsewhere.ask_namespace',
        description: "System can initiate transfer for namespaces. Can be many in one request ",
        summary: 'Try to get namespace from another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereAskingNamespace::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\AskNamespace::class)]
    public function ask_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/push_element',
        operationId: 'core.elsewhere.push_element',
        description: "System can send elements to server ",
        summary: 'Push element to another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewherePushingElement::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\PushElement::class)]
    public function push_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/push_set',
        operationId: 'core.elsewhere.push_set',
        description: "System can send a set with its elements to server ",
        summary: 'Push a set to another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewherePushingSet::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\PushSet::class)]
    public function push_set() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/push_namespace',
        operationId: 'core.elsewhere.push_namespace',
        description: "System can send a namespace to server ",
        summary: 'Push a namespace to another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewherePushingNamespace::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\PushNamespace::class)]
    public function push_namespace() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/push_type',
        operationId: 'core.elsewhere.push_type',
        description: "System can send a type to server ",
        summary: 'Push a type to another server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewherePushingType::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\PushType::class)]
    public function push_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/push_event',
        operationId: 'core.elsewhere.push_event',
        description: "System can send registered events to the other server ",
        summary: 'Push an event to another server to complete',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewherePushingEvent::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\PushEvent::class)]
    public function push_event() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/destroyed_element',
        operationId: 'core.elsewhere.destroyed_element',
        description: "We do record keeping, so if this element is recorded as sent to that server, event is called. ".
        " \nThis is notification after the fact and up to us how to handle it ",
        summary: 'Server reports it destroyed an element we gave them',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereDestroyingElement::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\DestroyedElement::class)]
    public function destroyed_element() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Post(
        path: '/api/v1/{namespace}/elsewhere/suspended_type',
        operationId: 'core.elsewhere.suspended_type',
        description: "The other server can suspend a type we gave them, or they gave us. Its up to here how to handle it, can be blocked by events ",
        summary: 'Server suspended type we share with them',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiEventMarker( Evt\Elsewhere\ElsewhereSuspendingType::class)]
    #[ApiAccessMarker( TypeOfAccessMarker::CALLING_SERVER)]
    #[ApiTypeMarker( Root\Api\Elsewhere\SuspendedType::class)]
    public function suspended_type() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }





    #[OA\Delete(
        path: '/api/v1/{namespace}/elsewhere/purge_elsewhere',
        operationId: 'core.elsewhere.purge_elsewhere',
        description: "System can remove all associated types,elements,namespaces,sets and designs based on that. No events raised",
        summary: 'System can remove server and all its associated',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\Purge::class)]
    public function purge_elsewhere() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Get(
        path: '/api/v1/elsewhere/list_servers',
        operationId: 'core.elsewhere.list_servers',
        description: "Anyone can filter servers with a path, and get a paginated list",
        summary: 'List the servers',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Elsewhere\ListElsewhere::class)]
    public function list_servers() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/elsewhere/show',
        operationId: 'core.elsewhere.show',
        description: "Anyone can see public information about a server",
        summary: 'Information about a server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::IS_PUBLIC)]
    #[ApiTypeMarker( Root\Api\Elsewhere\Show::class)]
    public function show_elsewhere_public(): JsonResponse {
        return response()->json([], CodeOf::HTTP_SERVICE_UNAVAILABLE);
    }


    #[OA\Get(
        path: '/api/v1/elsewhere/admin_elsewhere',
        operationId: 'core.elsewhere.admin_elsewhere',
        description: "Provides links and detailed information about a particular server",
        summary: 'System has private details about a server',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    #[ApiAccessMarker( TypeOfAccessMarker::SYSTEM)]
    #[ApiTypeMarker( Root\Api\Elsewhere\ShowAdmin::class)]
    public function show_admin_elsewhere(): JsonResponse {
        return response()->json([], CodeOf::HTTP_SERVICE_UNAVAILABLE);
    }





}
