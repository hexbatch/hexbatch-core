<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class ThingController extends Controller {

    public function thing_hook_list() {
        //todo thing api calls (admin only), these never go through the thing queue, so there are no actions or api registered in the types, like the users
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_hook_create() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_hook_show() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_hook_edit() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_hook_remove() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_add_breakpoint() { //(to the exact thing)
        /*
         todo * manage single stepping children with parent hooked to debugging
                the breakpoints on are the things, and do not change status
                   the parent nodes set to debugging will get the notice
                   if nothing set, then the thing will just stop and wait
         */
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_clear_breakpoint() { //(clears on thing and all down-thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_run() { //(on breaking  thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_single_step() { //(on breaking thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


    public function thing_list() { //(top roots) also search
        //todo list/search/view thing nodes and trees
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_show() { //  (a tree)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_inspect() { //  (a single thing)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_pause() { //todo thing_pause|unpause for making sure the thing will wait for the debugging, or when not needed anymore
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_unpause() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_trim_tree() {  //(if child will return false to parent when it runs, if root then its just gone)
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_rate_apply() {
        //todo Apply|Remove|List rates to set|type|action|namespace|thing
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_rate_remove() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    public function thing_rate_list() { //also search
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }


}
