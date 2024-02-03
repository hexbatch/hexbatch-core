<?php

namespace App\Models;

use App\Models\Enums\AttributeUserGroupType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
    protected $fillable = [

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
    protected $casts = [
        'group_type' => AttributeUserGroupType::class,
    ];

    public function group_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','group_parent_attribute_id');
    }

    public function target_user_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','target_user_group_id');
    }
}
