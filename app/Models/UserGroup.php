<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ResourceNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
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
    public $timestamps = false;
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
        $ret = null;
        try {
            if ($field) {
                $ret = $this->where($field, $value)->first();
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $ret = $this->where('ref_uuid', $value)->first();
                } else {
                    if (is_string($value)) {
                        //the name, but scope to the user id of the owner
                        //if this user is not the owner, then the group owner id can be scoped
                        $parts = explode('.', $value);
                        if (count($parts) === 1) {
                            //must be owned by the user
                            $user = auth()->user();
                            $ret = $this->where('user_id', $user?->id)->where('group_name', $value)->first();
                        } else {
                            $owner = $parts[0];
                            $maybe_name = $parts[1];
                            $owner = (new User)->resolveRouteBinding($owner);
                            $ret = $this->where('user_id', $owner?->id)->where('group_name', $maybe_name)->first();
                        }


                    }
                }
            }
        } finally {
            if (empty($ret)) {
                throw new HexbatchNotFound(
                    __('msg.group_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::GROUP_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public static function buildGroup(int $user_id) : Builder {
        return UserGroup::select('user_groups.*','user_group_members.is_admin')
            /** @uses UserGroup::group_owner() */
            ->with('group_owner')
            ->join('user_group_members',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($user_id) {
                    $join
                        ->on('user_groups.id','=','user_group_members.user_group_id')
                        ->where('user_group_members.user_id',$user_id);
                }
            );
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
