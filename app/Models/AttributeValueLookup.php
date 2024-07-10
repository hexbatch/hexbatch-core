<?php

namespace App\Models;


use App\Models\Enums\Attributes\AttributeUserGroupType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;




/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int value_lookup_attribute_id
 * @property int value_managed_by_attribute_id
 * @property int value_lookup_value_id

 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property AttributeValue lookup_value
 */
class AttributeValueLookup extends Model
{
    protected $table = 'attribute_user_group_lookups';
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

    public function value_lookup() : BelongsTo {
        return $this->belongsTo('App\Models\AttributeValue','value_lookup_value_id');
    }


}
