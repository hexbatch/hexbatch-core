<?php

namespace App\Helpers\UserGroups;

use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\UserGroup;
use Illuminate\Validation\ValidationException;

class GroupGathering
{
    /**
     * @param $group_name
     * @param null $attribute_name
     * @return UserGroup
     * @throws ValidationException
     */
    public static function SetupNewGroup($group_name,$attribute_name = null) : UserGroup {
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
        $group->refresh();
        return $group;
    }

}
