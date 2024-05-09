<?php

namespace App\Http\Controllers\API;


use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\RemoteStackCollection;
use App\Http\Resources\RemoteStackResource;
use App\Models\Enums\Remotes\RemoteStackCategoryType;
use App\Models\Remote;
use App\Models\RemoteStack;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




class StackController extends Controller
{
    /**
     * @uses Remote::remote_owner()
     */
    protected function adminCheck(RemoteStack $stack) {
        $user = Utilities::getTypeCastedAuthUser();
        $stack->stack_owner->checkAdminGroup($user->id);
    }

    public  static function stackUsageCheck(?RemoteStack $stack) {
        if (!$stack) {return;}

        $user = Utilities::getTypeCastedAuthUser();
        if ($stack->stack_owner->user_group->isMember($user->id)) {
            return;
        }

        throw new HexbatchPermissionException(__("msg.stack_not_in_usage_group",['ref'=>$stack->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
            RefCodes::USER_NOT_PRIV);
    }




    public function show_stack(RemoteStack $remote_stack) {
        return response()->json(new RemoteStackResource($remote_stack,null,30), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function execute_stack(RemoteStack $remote_stack) {
        static::stackUsageCheck($remote_stack);
        $remote_stack->execute_stack(RemoteStackCategoryType::MAIN);
        $refresh = RemoteStack::buildRemoteStack(id:$remote_stack->id)->first();
        return response()->json(new RemoteStackResource($refresh,null,30), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function append_stack(RemoteStack $child_stack,?RemoteStack $parent_stack) {
        static::stackUsageCheck($parent_stack);
        static::stackUsageCheck($child_stack);
        $child_stack->parent_remote_stack_id = $parent_stack?->id;

        $child_stack->save();
        $child_stack->refresh();
        return response()->json(new RemoteStackResource($child_stack,null,30), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function create_stack(Request $request,?RemoteStack $remote_stack = null) {
        $test_stack = new RemoteStack();
        $test_stack->user_id = Auth::id();
        if($remote_stack) {
            $test_stack->parent_remote_stack_id = $remote_stack->id;
        }

        if ($request->request->has('category')) {
            $test_stack->remote_stack_category = RemoteStackCategoryType::tryFrom($request->request->get('category'));
            if (empty($test_stack->remote_stack_category)) {
                $test_stack->remote_stack_category = RemoteStackCategoryType::MAIN;
            }
        } else {
            $test_stack->remote_stack_category = RemoteStackCategoryType::MAIN;
        }

        if ($request->has('starting_activity_data')) {
            $test_stack->starting_activity_data = $request->get('starting_activity_data');
        }

        $test_stack->save();
        return response()->json(new RemoteStackResource($test_stack,null,30), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }



    public function stack_list(?User $user = null) {
        $logged_user = auth()->user();
        if (!$user) {$user = $logged_user;}
        $out_laravel = Remote::buildRemote(usage_user_id: $user->id);
        $out = $out_laravel->cursorPaginate();
        return response()->json(new RemoteStackCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function stack_delete(RemoteStack $stack) {
        $this->adminCheck($stack);
        $stack->delete();
        return response()->json(new RemoteStackResource($stack), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }






}
