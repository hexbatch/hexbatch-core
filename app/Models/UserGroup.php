<?php

namespace App\Models;

use App\Helpers\Utilities;
use App\Rules\ResourceNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int user_id
 * @property string group_name
 * @property string created_at
 * @property string updated_at
 *
 * @property UserGroupMember[] group_members
 * @property UserGroupMember[] group_admins
 * @property User group_owner
 */
class UserGroup extends Model
{

    protected $table = 'user_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_retired',
        'group_name',
        'user_id'
    ];

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
    protected $casts = [];

    public function group_members() : HasMany {
        return $this->hasMany('App\Models\UserGroupMember')
            /** @uses \App\Models\UserGroupMember::member_user() */
            ->with('member_user')
            ->orderBy('created_at');
    }

    public function group_admins() : HasMany {
        return $this->hasMany('App\Models\UserGroupMember')
            ->where('is_admin',true)
            /** @uses \App\Models\UserGroupMember::member_user() */
            ->with('member_user')
            ->orderBy('updated_at');
    }

    public function group_owner() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        } else {
            if (Utilities::is_uuid($value)) {
                //the ref
                return $this->where('ref_uuid',$value)->firstOrFail();
            } else {
                //the name, but scope to the user id logged in
                /**
                 * @var User $user
                 */
                $user = auth()->user();
                return $this->where('user_id', $user?->id)->where('group_name',$value)->firstOrFail();
            }
        }

    }


    /**
     * @param string|null $group_name
     * @return void
     * @throws ValidationException
     */
    public function setGroupName(?string $group_name) {
        Validator::make(['group_name'=>$group_name], [
            'group_name'=>['required','string','max:128',new ResourceNameReq],
        ])->validate();
        $this->group_name = $group_name;
    }

    public function isAdmin(?int $user_id) : ?UserGroupMember {
        if (!$user_id) {return null;}
        return UserGroupMember::where('user_group_id',$this->id)->where('user_id',$user_id)->where('is_admin',true)->first();
    }

    public function isMember(?int $user_id) : ?UserGroupMember {
        if (!$user_id) {return null;}
        return UserGroupMember::where('user_group_id',$this->id)->where('user_id',$user_id)->first();
    }

    public function addMember(int $user_id,bool $is_admin=false) : UserGroupMember {
        $member = new UserGroupMember();
        $member->user_id = $user_id;
        $member->user_group_id = $this->id;
        $member->is_admin = $is_admin;
        $member->save();
        $member->refresh();
        return $member;
    }

    public function removeMember(int $user_id) : ?UserGroupMember {
        $member = $this->isMember($user_id);
        $member?->delete();
        return $member;
    }
}
