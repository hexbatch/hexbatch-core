<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Attributes\AttributeUserGroupType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int group_parent_attribute_id
 * @property int target_user_group_id
 * @property AttributeUserGroupType group_type
 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property UserGroup target_user_group
 * @property Attribute group_parent
 */
class AttributeUserGroup extends Model
{
    protected $table = 'attribute_user_groups';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'group_type' => AttributeUserGroupType::class,
    ];

    public function group_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','group_parent_attribute_id');
    }

    public function target_user_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','target_user_group_id');
    }

    public bool $delete_mode = false;

    public static function createUserGroup(string|array $group_hint,AttributeUserGroupType $group_type,?Attribute $parent = null) : AttributeUserGroup {
        $ret = new AttributeUserGroup();
        $ret->group_type = $group_type;
        if ($parent) {
            $ret->group_parent_attribute_id = $parent->id;
        }
        $use_group_hint = $group_hint;
        if (is_array($group_hint)) {
            if (!array_key_exists('group',$group_hint)) {
                throw new HexbatchNotPossibleException(__("msg.attribute_schema_missing_permission_group"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
            $use_group_hint = $group_hint['group'];
            if (array_key_exists('delete',$group_hint) && Utilities::boolishToBool($group_hint['delete'])) {
                $ret->delete_mode = true;
            }
        }
        /**
         * @var UserGroup $user_group
         */
        $user_group = (new UserGroup())->resolveRouteBinding($use_group_hint);
        if (!$user_group->isAdmin(Auth::id())) {
            throw new HexbatchNotPossibleException(__("msg.attribute_schema_need_admin_permission_group",['group_name'=>$user_group->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }
        $ret->target_user_group_id = $user_group->id;
        return $ret;
    }

    public function deleteModeActivate() {
        if ($this->delete_mode) {
            AttributeUserGroup::where('group_parent_attribute_id',$this->group_parent_attribute_id)
                ->where('target_user_group_id',$this->target_user_group_id)
                ->where('group_type',$this->group_type->value)
                ->delete();
        }
    }


}
