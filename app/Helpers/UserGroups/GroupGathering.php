<?php

namespace App\Helpers\UserGroups;

use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\User;
use App\Models\UserGroup;


class GroupGathering
{
    /**
     * @param $group_name
     * @param $attribute_name
     * @param array $members
     * @param array $admins
     * @return UserGroup
     */
    public static function SetupNewGroup($group_name, $attribute_name = null, array $members = [], array $admins = []) : UserGroup {
        $user = Utilities::getTypeCastedAuthUser();
        $group = new UserGroup();
        $group->setGroupName($group_name,$attribute_name);
        $conflict =  UserGroup::where('user_id', $user?->id)->where('group_name',$group_name)->first();
        if ($conflict) {
            throw new HexbatchNameConflictException(__("msg.unique_resource_name_per_user",['resource_name'=>$group->group_name]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RESOURCE_NAME_UNIQUE_PER_USER);
        }
        $group->user_id = $user->id;
        $group->save();
        $group->addMember($user->id,true);


        foreach ($members as $who) {
            /** @var User $member */
            $member =  (new User)->resolveRouteBinding($who);
            $group->addMember($member->id);
        }

        foreach ($admins as $who) {
            /** @var User $member */
            $member =  (new User)->resolveRouteBinding($who);
            $group->addMember($member->id,true);
        }

        $ret = UserGroup::buildGroup(group_id:$group->id)->first();
        return $ret;
    }

    public static function adminCheckOrMakeGroupWithUserAdmin($ref_object_or_array) : ?UserGroup{
        if (empty($ref_object_or_array)) {return null;}

        $user = Utilities::getTypeCastedAuthUser();

        if (is_string($ref_object_or_array)) {
            /**
             * @var UserGroup $user_group
             */
            $user_group = (new UserGroup())->resolveRouteBinding($ref_object_or_array);
            if(!$user_group->isAdmin($user->id)) {
                throw new HexbatchPermissionException(__('msg.group_not_admin',['username'=>$user->getName(),'group'=>$user_group->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                    RefCodes::ELEMENT_TYPE_INVALID_GROUP);
            }
        } else {
            $group_name = null;
            $members = [];
            $admins = [];
            GroupGathering::parseNameAdminsMembers($ref_object_or_array,$group_name,$members,$admins);

            if (!$group_name) {
                throw new HexbatchPermissionException(__('msg.group_new_has_no_name'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                    RefCodes::ELEMENT_TYPE_INVALID_GROUP);
            }
            //see if existing group and if user is admin
            try {
                /** @var UserGroup $user_group */
                $user_group = (new UserGroup())->resolveRouteBinding($group_name);
                if(!$user_group->isAdmin($user->id)) {
                    throw new HexbatchPermissionException(__('msg.group_not_admin',['username'=>$user->getName(),'group'=>$user_group->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                        RefCodes::ELEMENT_TYPE_INVALID_GROUP);
                }
            } catch (HexbatchNotFound ) {
                $user_group = GroupGathering::SetupNewGroup($group_name,null,$members,$admins);
            }

        }
        return $user_group;
    }

    protected static function parseNameAdminsMembers(array|object $what,?string &$group_name,array &$members,array &$admins) {
        $group_name = null;
        $members = [];
        $admins = [];
        if(is_array($what)) {
            $group_name = $what['group_name']??null;
        }
        if(is_object($what)) {
            $group_name = $what->group_name??null;
        }

        if(is_array($what)) {
            $members = $what['members']??[];
        }
        if(is_object($what)) {
            $members = $what->members??[];
        }

        if(is_array($what)) {
            $admins = $what['admins']??[];
        }
        if(is_object($what)) {
            $admins = $what->admins??[];
        }
    }

}
