<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int user_group_id
 * @property int member_user_type_id
 * @property bool is_admin
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property UserType member_user
 * @property UserGroup parent_group
 *
 */
class UserGroupMember extends Model
{
    //look to the user private element to see if any events on this for groups
    /*
     *      'user_group_member_add', a user can deny him being added to a group as a member, group maintainers can automate stuff
            'user_group_admin_add', a user can deny him being added to a group as an admin, also automation
            'user_admin_removing_member', an admin can stop a member being removed (who is not an admin)
            'user_owner_removing_admin', the group owner can automate some stuff when removing an admin
     */
    protected $table = 'user_group_members';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_user_type_id',
        'user_group_id',
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

    public function member_user() : BelongsTo {
        return $this->belongsTo(UserType::class,'member_user_type_id');
    }

    public function parent_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','user_group_id');
    }
}
