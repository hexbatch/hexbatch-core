<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response as CodeOf;
use OpenApi\Attributes as OA;

class ThingController extends Controller {


    #[OA\Get(
        path: '/api/v1/{namespace}/things/hooks/list',
        operationId: 'core.things.hooks.list',
        description: "",
        summary: 'List all the hooks',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_hook_list() {
        //todo thing api calls (admin only), these never go through the thing queue, so there are no actions or api registered in the types, like the users
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{namespace}/things/hooks/create',
        operationId: 'core.things.hooks.create',
        description: "",
        summary: 'Create a new hook',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_hook_create() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/things/hooks/show',
        operationId: 'core.things.hooks.show',
        description: "",
        summary: 'Shows information about a hook',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_hook_show() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Patch(
        path: '/api/v1/{namespace}/things/hooks/edit',
        operationId: 'core.things.hooks.edit',
        description: "",
        summary: 'Edits a hook',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_hook_edit() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Delete(
        path: '/api/v1/{namespace}/things/hooks/destroy',
        operationId: 'core.things.hooks.destroy',
        description: "",
        summary: 'Edits a hook',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_hook_destroy() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{namespace}/things/debugging/breakpoint',
        operationId: 'core.things.debugging.breakpoint',
        description: "",
        summary: 'Add a breakpoint',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_add_breakpoint() { //(to the exact thing)
        /*
         todo * manage single stepping children with parent hooked to debugging
                the breakpoints on are the things, and do not change status
                   the parent nodes set to debugging will get the notice
                   if nothing set, then the thing will just stop and wait
         */
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Delete(
        path: '/api/v1/{namespace}/things/debugging/clear',
        operationId: 'core.things.debugging.clear_breakpoint',
        description: "",
        summary: 'Removes a breakpoint',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_clear_breakpoint() { //(clears on thing and all down-thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{namespace}/things/debugging/run',
        operationId: 'core.things.debugging.run',
        description: "",
        summary: 'Runs from a stopped thing',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_run() { //(on breaking  thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{namespace}/things/debugging/single_step',
        operationId: 'core.things.debugging.single_step',
        description: "",
        summary: 'Single steps from a stopped thing',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_single_step() { //(on breaking thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Patch(
        path: '/api/v1/{namespace}/things/debugging/pause',
        operationId: 'core.things.debugging.pause',
        description: "",
        summary: 'Sets the thing to not run automatically',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_pause() { //todo thing_pause|unpause for making sure the thing will wait for the debugging, or when not needed anymore
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Patch(
        path: '/api/v1/{namespace}/things/debugging/unpause',
        operationId: 'core.things.debugging.unpause',
        description: "No effect if not already paused",
        summary: 'Sets a thing to be automatically run',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_unpause() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



    #[OA\Get(
        path: '/api/v1/{namespace}/things/list',
        operationId: 'core.things.list',
        description: "",
        summary: 'Lists things, searchable',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_list() { //(top roots) also search
        //todo list/search/view thing nodes and trees
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Get(
        path: '/api/v1/{namespace}/things/show',
        operationId: 'core.things.show',
        description: "",
        summary: 'Shows a thing and its descendants',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_show() { //  (a tree)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/things/inspect',
        operationId: 'core.things.inspect',
        description: "",
        summary: 'Inspects a single thing',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_inspect() { //  (a single thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }




    #[OA\Delete(
        path: '/api/v1/{namespace}/things/trim',
        operationId: 'core.things.trim',
        description: "Parents of trimmed things will return false",
        summary: 'Removes a thing and its descendants',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_trim_tree() {  //(if child will return false to parent when it runs, if root then its just gone)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Post(
        path: '/api/v1/{namespace}/things/rates/apply',
        operationId: 'core.things.rates.apply',
        description: "",
        summary: 'Applies a rate(s) to a thing and its descendants',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_rate_apply() {
        //todo Apply|Remove|List rates to set|type|action|namespace|thing
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{namespace}/things/rates/remove',
        operationId: 'core.things.rates.remove',
        description: "",
        summary: 'Removes a rate(s) to a thing and its descendants',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_rate_remove() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    #[OA\Get(
        path: '/api/v1/{namespace}/things/rates/list',
        operationId: 'core.things.rates.list',
        description: "",
        summary: 'Lists the rates that apply to a thing and its descendants',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_rate_list() { //also search
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Get(
        path: '/api/v1/{namespace}/things/rates/show',
        operationId: 'core.things.rates.show',
        description: "",
        summary: 'Shows information about a setting/rate',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_rate_show() { //also search
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[OA\Post(
        path: '/api/v1/{namespace}/things/rates/edit',
        operationId: 'core.things.rates.edit',
        description: "",
        summary: 'Edit a setting/rate',
        parameters: [new OA\PathParameter(  ref: '#/components/parameters/namespace' )],
        responses: [
            new OA\Response( response: CodeOf::HTTP_NOT_IMPLEMENTED, description: 'Not yet implemented')
        ]
    )]
    public function thing_rate_edit() { //also search
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
