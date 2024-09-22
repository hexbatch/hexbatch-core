<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int creator_attribute_id

 * @property string created_at
 * @property string updated_at
 *
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property Attribute creator_attribute

 *
 */
class AttributeRuleBundle extends Model
{

    protected $table = 'attribute_rule_bundles';
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
    protected $casts = [];


    public function creator_attribute() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','creator_attribute_id');
    }

    public function rules_in_group() : HasMany {
        return $this->hasMany('App\Models\AttributeRule','rule_bundle_owner_id');
    }


}
