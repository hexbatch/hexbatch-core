<?php

namespace App\Models;


use App\Enums\Attributes\AttributeRuleType;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int creator_attribute_id
 * @property string ref_uuid
 * @property string created_at
 * @property string updated_at
 *
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property Attribute creator_attribute
 * @property AttributeRule[] rules_in_group
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
        return $this->hasMany('App\Models\AttributeRule','rule_bundle_owner_id')
            /** @uses AttributeRule::rule_target(),AttributeRule::rule_group(), */
            /** @uses AttributeRule::rule_location_bound(),AttributeRule::rule_time_bound() */
            ->with('rule_target','rule_group','rule_location_bound','rule_time_bound')
            ->orderBy('rule_type');
    }

    /**
     * @param User|null $user
     * @return bool
     * @uses AttributeRuleBundle::rules_in_group()
     */
    public function canUserSee(?User $user = null) : bool {
        if (!$user) { $user = Utilities::getTypeCastedAuthUser();}
        $sum = 0;
        $count = 0;
        foreach ($this->rules_in_group as $rule) {
            if ($rule->rule_type === AttributeRuleType::READ) {
                $count++;
                if ($rule->rule_group?->isMember($user->id)) {
                    $sum += $rule->rule_weight * $rule->rule_value;
                }
            }
        }
        if (!$count || $sum >= 0) { return true;}
        return false;
    }


}
