<?php

namespace App\Models;

use App\Models\Enums\AttributeRuleType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int rule_parent_attribute_id
 * @property int target_attribute_id
 * @property int rule_weight
 * @property int rule_numeric_min
 * @property int rule_numeric_max
 * @property string rule_regex
 * @property AttributeRuleType rule_type
 * @property string created_at
 * @property string updated_at
 *
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property Attribute rule_parent
 * @property Attribute rule_target
 *
 */
class AttributeRule extends Model
{

    protected $table = 'attribute_rules';
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
        'rule_type' => AttributeRuleType::class,
    ];

    public function rule_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','rule_parent_attribute_id');
    }

    public function rule_target() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','target_attribute_id');
    }

}
