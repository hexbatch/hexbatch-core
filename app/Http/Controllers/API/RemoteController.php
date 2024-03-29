<?php

namespace App\Http\Controllers\API;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Remotes\Build\DataGathering;
use App\Helpers\Remotes\Build\GroupTypeGathering;
use App\Helpers\Remotes\Build\RemoteAlwaysCanSetOptions;
use App\Helpers\Remotes\Build\RemoteUriGathering;
use App\Helpers\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\RemoteActivityCollection;
use App\Http\Resources\RemoteActivityResource;
use App\Http\Resources\RemoteCollection;
use App\Http\Resources\RemoteResource;
use App\Http\Resources\RemoteStackResource;
use App\Models\AttributeValuePointer;
use App\Models\Enums\Attributes\AttributeValueType;
use App\Models\Enums\Remotes\RemoteActivityStatusType;
use App\Models\Enums\Remotes\RemoteStackCategoryType;
use App\Models\Enums\Remotes\RemoteUriType;
use App\Models\Remote;
use App\Models\RemoteActivity;
use App\Models\RemoteStack;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;



class RemoteController extends Controller
{
    /**
     * @uses Remote::remote_owner()
     */
    protected function adminCheck(Remote $att) {
        $user = Utilities::getTypeCastedAuthUser();
        $att->remote_owner->checkAdminGroup($user->id);
    }

    protected function usageCheck(Remote $remote) {
        $user = Utilities::getTypeCastedAuthUser();
        if ($remote->remote_owner->user_group->isMember($user->id) || $remote->usage_group->isMember($user->id)) {
            return;
        }

        throw new HexbatchPermissionException(__("msg.remote_not_in_usage_group",['ref'=>$remote->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
            RefCodes::USER_NOT_PRIV);
    }

    protected function elementCheck(RemoteActivity $activity) {

        if ($activity->caller_element_id) {
            $user = Utilities::getTypeCastedAuthUser();
            if ($activity->caller_element->element_owner->user_group->isAdmin($user->id) ) {
                return;
            }
        }

        throw new HexbatchPermissionException(__("msg.remote_activity_not_owned_by_your_element",['ref'=>$activity->caller_element->getName()]),
            \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
            RefCodes::USER_NOT_PRIV);
    }

    public function remote_get(Remote $remote,?string $full = null) {
        $this->usageCheck($remote);
        $n_level = (int)$full;
        if ($n_level <= 0) { $n_level =0;}
        return response()->json(new RemoteResource($remote,null,$n_level), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function show_stack(RemoteStack $remote_stack) {
        return response()->json(new RemoteStackResource($remote_stack,null,30), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function execute_stack(RemoteStack $remote_stack) {
        $remote_stack->execute_stack(RemoteStackCategoryType::MAIN);
        $refresh = RemoteStack::buildRemoteStack(id:$remote_stack->id)->first();
        return response()->json(new RemoteStackResource($refresh,null,30), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function create_test_stack(Request $request,?RemoteStack $remote_stack = null) {
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

    /**
     * @throws \Exception
     */
    public function remote_test(Request $request, Remote $remote,?RemoteStack $remote_stack = null) {
        $this->usageCheck($remote);
        $inputs = $request->collect();
        $user = null;$type = null;$action = null;$element = null; $attribute = null;
        if ($inputs->has('callers')) {
            $callers = new Collection($inputs->get('callers'));
            if ($callers->has('user')) {
                $user = AttributeValuePointer::getModelFromHint($callers->get('user'),AttributeValueType::USER);
            }
            if ($callers->has('type')) {
                $type = AttributeValuePointer::getModelFromHint($callers->get('type'),AttributeValueType::ELEMENT_TYPE);
            }
            if ($callers->has('action')) {
                $action = AttributeValuePointer::getModelFromHint($callers->get('action'),AttributeValueType::ACTION);
            }
            if ($callers->has('element')) {
                $element = AttributeValuePointer::getModelFromHint($callers->get('element'),AttributeValueType::ELEMENT);
            }
            if ($callers->has('attribute')) {
                $attribute = AttributeValuePointer::getModelFromHint($callers->get('attribute'),AttributeValueType::ATTRIBUTE);
            }
            $inputs->forget('callers');
        }
        $debugging = null;
        if ($inputs->has('debugging')) {
            $debugging = (new Collection($inputs->get('debugging')) )->toArray();
            $inputs->forget('debugging');
        }


        $activity = $remote->createActivity(collection: $inputs, user: $user,
            type: $type, element: $element, attribute: $attribute, action: $action,pass_through: $debugging);

        if ($remote_stack) {
            $activity->remote_stack_id = $remote_stack->id;
            $activity->save();
        }
        else
        {
            $remote_stack = new RemoteStack();
            $remote_stack->remote_stack_category = RemoteStackCategoryType::MAIN;
            $remote_stack->user_id = Auth::id();
            $remote_stack->save();
            $remote_stack->execute_stack(RemoteStackCategoryType::MAIN);
        }


        $display_activity = RemoteActivity::buildActivity(id:$activity->id)->first();
        return response()->json(new RemoteActivityResource($display_activity,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function update_activity(Request $request, RemoteActivity $remote_activity) {
        //see if valid activity
        /** @var RemoteActivity $checked_activity */
        $checked_activity = RemoteActivity::buildActivity(id:$remote_activity->id,remote_activity_status_type: RemoteActivityStatusType::PENDING)->first();
        if ($checked_activity) {
            throw new HexbatchNotPossibleException(__("msg.remote_activity_not_found"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::REMOTE_NOT_FOUND);
        }
        if (!in_array($checked_activity->remote_parent->uri_type,RemoteUriType::MANUAL_TYPES)) {
            throw new HexbatchNotPossibleException(__("msg.remote_activity_only_manual_updated",['ref'=>$checked_activity->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::REMOTE_NOT_FOUND);
        }

        if ($checked_activity->remote_parent->uri_type === RemoteUriType::MANUAL_ELEMENT) {
            $this->elementCheck($checked_activity);
        } elseif ($checked_activity->remote_parent->uri_type === RemoteUriType::MANUAL_OWNER) {
            $this->adminCheck($checked_activity->remote_parent);
        } else {
            throw new \LogicException("Other manual types not implemented yet");
        }

        $data = $request->collect();
        $checked_activity->doCallRemote($data);
        $out = RemoteActivity::buildActivity(id:$checked_activity->id)->first();
        return response()->json(new RemoteActivityResource($out,null,3), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
    public function list_activities(?RemoteActivityStatusType $remote_activity_status_type = null) {
        //either this is an admin or this is a user getting the usage things
        //just do usage now
        $remotes = Remote::buildRemote(usage_user_id: Utilities::getTypeCastedAuthUser()->id)->pluck('id')->toArray();
        $activities = RemoteActivity::buildActivity(remote_activity_status_type: $remote_activity_status_type,remote_id_array: $remotes)->cursorPaginate();
        return response()->json(new RemoteActivityCollection($activities), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }
    public function get_activity(RemoteActivity $remote_activity,?string $full = null) {
        $n_level = (int)$full;
        if ($n_level <= 0) { $n_level =0;}
        $this->usageCheck($remote_activity->remote_parent);
        $activity = RemoteActivity::buildActivity(id: $remote_activity->id)->first();
        return response()->json(new RemoteActivityResource($activity,null,$n_level), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function remote_list(?User $user = null) {
        $logged_user = auth()->user();
        if (!$user) {$user = $logged_user;}
        $out_laravel = Remote::buildRemote(usage_user_id: $user->id);
        $out = $out_laravel->cursorPaginate();
        return response()->json(new RemoteCollection($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    public function remote_delete(Remote $remote) {
        $this->adminCheck($remote);
        $remote->checkIsInUse();
        $out = Remote::buildRemote(id: $remote->id)->first();
        $remote->delete();
        return response()->json(new RemoteResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }


    /**
     * @param Remote $remote
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function remote_edit_patch(Remote $remote, Request $request) {
        $this->adminCheck($remote);

        try {
            DB::beginTransaction();
            // if this is in use then can only edit retired, meta
            // otherwise can edit all but the ownership
            if ($remote->isInUse()) {
                $this->updateInUseRemote($remote,$request);
            } else {
                $some_name = $request->request->getString('remote_name');

                if ($some_name && $some_name !== $remote->remote_name) {
                    $remote->setName($request->request->getString('remote_name'),Utilities::getTypeCastedAuthUser());
                }

                $this->updateAllRemote($remote,$request);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }



        $out = Remote::buildRemote(id: $remote->id)->first();

        return response()->json(new RemoteResource($out), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    protected function updateAllRemote(Remote $remote, Request $request) {

        (new RemoteUriGathering($request) )->assign($remote);
        $remote->save();
        $this->updateInUseRemote($remote,$request); //saved at this point

        (new DataGathering($request) )->assign($remote);
        (new GroupTypeGathering($request) )->assign($remote);

    }

    protected function updateInUseRemote(Remote $remote, Request $request) {
        (new RemoteAlwaysCanSetOptions($request) )->assign($remote);


    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function remote_create(Request $request): JsonResponse {


        try {
            DB::beginTransaction();
            // if this is in use then can only edit retired, meta
            // otherwise can edit all but the ownership
            $remote = new Remote();
            $user = Utilities::getTypeCastedAuthUser();
            $remote->setName($request->request->getString('remote_name'),$user);
            $remote->user_id = $user->id;
            $this->updateAllRemote($remote,$request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $out = Remote::buildRemote(id: $remote->id)->first();
        return response()->json(new RemoteResource($out,null,2), \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
