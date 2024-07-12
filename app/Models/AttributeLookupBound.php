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
 * @property int bound_lookup_attribute_id
 * @property int bound_managed_by_attribute_id
 * @property int bound_lookup_bound_id

 * @property string created_at
 * @property string updated_at
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property AttributeBound lookup_bound
 */
class AttributeLookupBound extends Model
{
    protected $table = 'attribute_bound_lookups';
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

    public function lookup_bound() : BelongsTo {
        return $this->belongsTo('App\Models\AttributeBound','bound_lookup_bound_id');
    }


}
