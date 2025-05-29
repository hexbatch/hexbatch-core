<?php

namespace App\Models;


use App\Enums\Types\TypeOfLiveRulePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;



/**
 * Rules defined at type design time
 * Approved in publish
 * Applied in each set the type's element's make
 * the apply_live in the rules will attempt to apply anything missing when that type enters or has extra lives added to it
 *
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int live_rule_owner_type_id
 * @property int live_rule_trigger_type_id
 * @property int live_rule_about_live_type_id
 * @property string ref_uuid
 * @property TypeOfLiveRulePolicy live_rule_policy
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class LiveRule extends Model
{

    protected $table = 'live_rules';
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
        'live_rule_policy' => TypeOfLiveRulePolicy::class,
    ];

}
