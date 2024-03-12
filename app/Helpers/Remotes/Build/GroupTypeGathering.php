<?php

namespace App\Helpers\Remotes\Build;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Remote;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class GroupTypeGathering
{
    protected ?UserGroup $usage_group;
    protected bool $delete_mode = false;

    public function __construct(Request $request)
    {
        $this->usage_group = null;

        if ($request->has('usage_group')) {
            $group_hint = $request->get('usage_group');
            if (is_array($group_hint)) {
                if (!array_key_exists('group',$group_hint)) {
                    throw new HexbatchNotPossibleException(__("msg.remote_schema_missing_permission_group"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);
                }
                $use_group_hint = $group_hint['group'];
                if (array_key_exists('delete',$group_hint) && Utilities::boolishToBool($group_hint['delete'])) {
                    $this->delete_mode = true;
                }
            } else {
                $use_group_hint = $group_hint;
            }
            /**
             * @var UserGroup $user_group
             */
            $user_group = (new UserGroup())->resolveRouteBinding($use_group_hint);
            if (!$user_group->isAdmin(Auth::id())) {
                throw new HexbatchNotPossibleException(__("msg.remote_schema_need_admin_permission_group",['group_name'=>$user_group->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            $this->usage_group = $user_group;
        }

    }

    public function assign(Remote $remote) {
        if ($this->usage_group) {
            if ($this->delete_mode) {
                $remote->usage_group = null;
            } else {
                $remote->usage_group_id = $this->usage_group->id;
            }
        }

    }
}
