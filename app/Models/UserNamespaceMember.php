<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_namespace_id
 * @property int member_namespace_id
 * @property bool is_admin
 *
 * @property UserNamespace parent_namespace
 * @property UserNamespace namespace_member
 *
 * @property string created_at
 * @property string updated_at
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 */
class UserNamespaceMember extends Model
{
    //look to the user private element to see if any events on this for groups
    /*
     *      'namespace_member_add', a user can deny him being added to a group as a member, group maintainers can automate stuff
            'namespace_adding_admin', a user can deny him being added to a group as an admin, also automation
            'namespace_removing_member', an admin can stop a member being removed (who is not an admin)
            'namespace_removing_admin', the group owner can automate some stuff when removing an admin
     */
    protected $table = 'user_namespace_members';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_namespace_id',
        'parent_namespace_id',
        'is_admin'
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

    public function namespace_member() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'member_namespace_id');
    }

    public function parent_namespace() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','parent_namespace_id');
    }

    public static function buildGroupMembers(?int $id = null,int $member_namespace_id = null, int $namespace_parent_id = null) : Builder {

        $build =  UserNamespaceMember::select('user_namespace_members.*')
            ->selectRaw(" extract(epoch from  user_namespace_members.created_at) as created_at_ts,  ".
                "extract(epoch from  user_namespace_members.updated_at) as updated_at_ts")
            /** @uses UserNamespaceMember::namespace_member() */
            ->with('namespace_member');

        if ($id) {
            $build->where('user_namespace_members.id', $id);
        }

        if ($member_namespace_id) {
            $build->where('user_namespace_members.member_namespace_id', $member_namespace_id);
        }

        if ($namespace_parent_id) {
            $build->where('user_namespace_members.parent_namespace_id',$namespace_parent_id);
        }
        return $build;
    }
}
