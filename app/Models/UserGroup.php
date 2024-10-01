<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Rules\ResourceNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owning_namespace_id
 * @property int group_element_id
 * @property string ref_uuid
 * @property string group_name
 * @property string created_at
 * @property string updated_at
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property UserNamespaceMember[] group_members
 * @property UserNamespaceMember[] group_admins
 * @property UserNamespace group_owner
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
        'owning_namespace_id'
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
        return $this->hasMany('App\Models\UserNamespaceMember','parent_namespace_id')
            /** @uses UserNamespaceMember::namespace_member */
            ->with('member_user')
            ->orderBy('created_at');
    }

    public function group_admins() : HasMany {
        return $this->hasMany('App\Models\UserNamespaceMember','parent_namespace_id')
            ->where('is_admin',true)
            /** @uses UserNamespaceMember::namespace_member() */
            ->with('member_user')
            ->orderBy('updated_at');
    }

    public function group_owner() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'owning_namespace_id');
    }

    public function getName() :string {
        return $this->group_owner->owner_user->getName() . UserNamespace::NAMESPACE_SEPERATOR. $this->group_name;
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
        $build = null;
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
                        /** @var UserNamespace $owner */
                        $owner = null;
                        $what_route = Route::current();
                        if ($what_route->hasParameter('user_namespace')) {
                            $owner = $what_route->parameter('user_namespace');

                            if (!$owner) {
                                $user_namespace_name = $what_route->originalParameter('user_namespace');
                                if ($user_namespace_name) {
                                    $owner = (new UserNamespace())->resolveRouteBinding($user_namespace_name);
                                }
                            }
                        }

                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);

                        if ($owner && count($parts) === 1) {
                            //it is the group name, scoped the namespace
                            $group_name = $parts[0];
                            $build = $this->where('owning_namespace_id', $owner->id)->where('group_name', $group_name);
                        } else if (count($parts) === 2) {
                            // it is the owner.group
                            $user_namespace_name = $parts[0];
                            $group_name = $parts[1];
                            /** @var UserNamespace $owner */
                            $owner = (new UserNamespace())->resolveRouteBinding($user_namespace_name);
                            $build = $this->where('owning_namespace_id', $owner?->id)->where('group_name', $group_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $first_build = UserGroup::buildGroup(group_id: $first_id);
                    $ret = $first_build->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Group resolving: '. $e->getMessage());
        }
        finally {
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

    public static function buildGroup(int $owner_namespace_id = null,int $member_namespace_id = null, int $group_id = null) : Builder {

        $build =  UserGroup::select('user_groups.*')
            ->selectRaw(" extract(epoch from  user_groups.created_at) as created_at_ts,  extract(epoch from  user_groups.updated_at) as updated_at_ts")
            /** @uses UserGroup::group_owner() */
            ->with('group_owner');

            if ($owner_namespace_id) {
                $build->where('user_groups.owning_namespace_id', $owner_namespace_id);
            }

            if ($member_namespace_id) {
                $build->
                join('user_namespace_members',
                    /**
                     * @param JoinClause $join
                     */
                    function (JoinClause $join) use ($member_namespace_id) {
                        $join
                            ->on('user_groups.id', '=', 'user_namespace_members.parent_namespace_id')
                            ->where('user_namespace_members.member_namespace_id', $member_namespace_id);
                    }
                );
            }

            if ($group_id) {
                $build->where('user_groups.id',$group_id);
            }
            return $build;
    }


    /**
     * @param string|null $group_name
     * @param string|null $attribute_name
     * @return void

     */
    public function setGroupName(?string $group_name,?string $attribute_name = null) {
        if (empty($attribute_name)) { $attribute_name = 'group_name';}

        try {
            Validator::make([$attribute_name => $group_name], [
                $attribute_name => ['required', 'string', 'max:128', new ResourceNameReq],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ELEMENT_TYPE_INVALID_NAME);
        }
        $this->group_name = $group_name;
    }

    public function isAdmin(?int $user_id) : ?UserNamespaceMember {
        if (!$user_id) {return null;}
        return UserNamespaceMember::where('parent_namespace_id',$this->id)->where('user_id',$user_id)->where('is_admin',true)->first();
    }

    public function isMember(?int $user_id) : ?UserNamespaceMember {
        if (!$user_id) {return null;}
        return UserNamespaceMember::where('parent_namespace_id',$this->id)->where('user_id',$user_id)->first();
    }

    public function addMember(int $user_id,bool $is_admin=false) : UserNamespaceMember {
        $member = new UserNamespaceMember();
        $member->user_id = $user_id;
        $member->parent_namespace_id = $this->id;
        $member->is_admin = $is_admin;
        $member->save();
        $member->refresh();
        return $member;
    }

    public function removeMember(int $user_id) : ?UserNamespaceMember {
        $member = $this->isMember($user_id);
        $member?->delete();
        return $member;
    }

    public function isInUse() : bool {
        if (!$this->id) {return false;}

        if( User::where('parent_namespace_id',$this->id)->exists() ) {return true;}
        return false;
    }
}
