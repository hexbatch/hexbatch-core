<?php

namespace App\Models;

use App\Enums\Attributes\AttributeRuleType;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int rule_bundle_owner_id
 * @property int target_attribute_id
 * @property int rule_user_group_id
 * @property int rule_time_bound_id
 * @property int rule_location_bound_id
 * @property string ref_uuid
 * @property string rule_name
 * @property int rule_weight
 * @property AttributeRuleType rule_type
 * @property int  rule_value
 * @property string rule_json_path
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property AttributeRuleBundle rule_owner
 * @property Attribute rule_target
 * @property UserGroup rule_group
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


    public function rule_owner() : BelongsTo {
        return $this->belongsTo('App\Models\AttributeRuleBundle','rule_bundle_owner_id');
    }

    public function rule_target() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','target_attribute_id');
    }

    public function rule_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','rule_user_group_id');
    }

    public function rule_time_bounds() : BelongsTo {
        return $this->belongsTo(TimeBound::class,'rule_time_bound_id');
    }

    public function rule_location_bounds() : BelongsTo {
        return $this->belongsTo(LocationBound::class,'rule_location_bound_id');
    }


    const DEFAULT_WEIGHT = 1;

    public bool $delete_mode = false;

    public static function createRule(string|array $rule_hint, AttributeRuleType $rule_type, ?Attribute $parent = null) : AttributeRule {
        $ret = new AttributeRule();
        $ret->rule_type = $rule_type;
        if ($parent) {
            $ret->rule_bundle_owner_id = $parent->id;
        }
        $use_rule_hint = $rule_hint;
        if (is_array($rule_hint)) {
            if (!array_key_exists('target',$rule_hint)) {
                throw new HexbatchNotPossibleException(__("msg.attribute_schema_missing_rule_attribute"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }
            $use_rule_hint = $rule_hint['target'];
            if (array_key_exists('delete',$rule_hint) && Utilities::boolishToBool($rule_hint['delete'])) {
                $ret->delete_mode = true;
            }

            if (array_key_exists('weight',$rule_hint) && !is_array($rule_hint['weight'])) {
                $ret->rule_weight = (int)$rule_hint['weight'];
            }


            if (array_key_exists('regex',$rule_hint) && !is_array($rule_hint['regex'])) {
                $rest_regex = $rule_hint['regex'];
                $bare_regex = trim($rest_regex, '/');
                $test_regex = "/$bare_regex/";
                $issues = Utilities::regexHasErrors($test_regex);
                if ($issues) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_rule_bad_regex", ['issue' => $issues]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
            }
        }
        /**
         * @var Attribute $found_attribute
         */
        $found_attribute = (new Attribute())->resolveRouteBinding($use_rule_hint);
        if (!$ret->delete_mode && $found_attribute->is_retired) {
            throw new HexbatchNotPossibleException(__("msg.attribute_schema_rule_retired",['name'=>$found_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
        }
        $ret->target_attribute_id = $found_attribute->id;
        return $ret;
    }

    public function deleteModeActivate() {
        if ($this->delete_mode) {
            AttributeRule::where('rule_bundle_owner_id',$this->rule_bundle_owner_id)
                ->where('target_attribute_id',$this->target_attribute_id)
                ->where('rule_type',$this->rule_type->value)
                ->delete();
        }
    }

}
