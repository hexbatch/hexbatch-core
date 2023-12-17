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
 * @property int user_id
 * @property bool is_admin
 * @property string created_at
 * @property string updated_at
 *
 * @property User member_user
 * @property UserGroup parent_group
 *
 */
class UserGroupMember extends Model
{

    protected $table = 'user_group_members';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_group_id',
        'user_id',
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
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function parent_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','user_group_id');
    }
}
