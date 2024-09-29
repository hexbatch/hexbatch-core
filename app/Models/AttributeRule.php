<?php

namespace App\Models;

use App\Enums\Attributes\AttributeRuleType;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JsonPath\JsonPath;

//todo add new fields for rules

//todo when debug_non_event_rules mode is set (config), then write what rule does to the attribute_rules_debugs table
// there should be another config to restrict this to an ancestor attribute that rule is on


/* Affinity membership depends on elements in a set to decide to join it when asked by command,
                 Affinity toggle can turn an attribute to not be readable or writable (both at the same time) in a set based on the contents
                 and the required is build time, so no checking there
                */

/*
 * Add to rules:

 *
 * --discussion
 *
 *  new types for triggers: the attribute changes value (any value), be turned off, or turned on.
 *
 *  The trigger has to be something not whitelisted and the user is not on that . The target must be editable.
 *  Remotes can be linked together in a tree, with each node doing its own bool logic from the children (and or xor)
 * so add bool logic column to the remotes, as well as a parent remote.
 *  Rules in a tree do not have to do anything except for the root, but any node can do a regular rule if its bool from children is truthful
 *  Independent rules just listen to triggers
 *  Need new api to copy over all the rules from one attribute to another, and not just one at a time.
 *
 * Remote chains and unrelated can be saved per attribute, and that attribute copied.
 * Remote chains can listen to ancestors so can be reused for different things.
 * todo rules can be edited even if the type is in use and has elements, this is because rules are static
 *
 * todo when rule rejects attr or needs attr, the response should list what is needed and why
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int rule_bundle_owner_id
 * @property int rule_trigger_attribute_id
 * @property string ref_uuid
 * @property string rule_name
 * @property int rule_weight
 * @property AttributeRuleType rule_type
 * @property int  rule_value
 * @property string rule_json_path
 * @property bool target_descendant_range
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 *
 * @property AttributeRuleBundle rule_owner
 * @property Attribute rule_target
 *
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
        return $this->belongsTo('App\Models\Attribute','rule_trigger_attribute_id');
    }



    public function getName() : string {
        if ($this->rule_name) {return $this->rule_name;}
        return $this->ref_uuid;
    }

    public static function buildAttributeRule(
        ?int $id = null,
    )
    : Builder
    {

        $build = ElementType::select('attribute_rules.*')
            ->selectRaw(" extract(epoch from  attribute_rules.created_at) as created_at_ts,  extract(epoch from  attribute_rules.updated_at) as updated_at_ts")

            /** @uses AttributeRule::rule_target() */
            /** @uses AttributeRule::rule_owner() */
            ->with('rule_target','rule_owner')
        ;

        if ($id) {
            $build->where('attribute_rules.id', $id);
        }


        return $build;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $build = null;
        $ret = null;
        try {
            if ($field) {
                $ret = $this->where($field, $value)->first();
            } else {
                if (Utilities::is_uuid($value)) {
                    $build = $this->where('ref_uuid', $value);
                }
            }

            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $first_build = AttributeRule::buildAttributeRule(id: $first_id);
                    $ret = $first_build->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Attribute rule resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret)) {
                throw new HexbatchNotFound(
                    __('msg.rule_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::RULE_NOT_FOUND
                );
            }
        }
        return $ret;
    }

    public function checkRequired(ElementType $type) : bool{
        if ($this->rule_type !== AttributeRuleType::REQUIRED) {
            Log::warning("checkRequired is being run on a different rule type: ".$this->rule_type->value);
            return true;
        }
        if (!$this->rule_trigger_attribute_id) {return true;}
        $total_sum = 0;
        //see if the target matches any attribute in the horde

        //get all the descendants of the attribute in the horde, unless target_descendant_range
        if($this->target_descendant_range) {
            $builder = ElementTypeHorde::getDecendants($this->rule_target);
        } else {
            /** @var ElementTypeHorde $horde_found */
            $horde_found = $type->type_hordes()->where('horde_attribute_id',$this->rule_trigger_attribute_id)->first();
            if (!$horde_found) {return true;}
            $builder = Attribute::where('id',$horde_found->horde_attribute_id);
        }

        $builder
            ->chunk(200, function (Collection $attributes) use(&$total_sum)  {

                /**
                 * @var Attribute $attr
                 */
            foreach ($attributes as $attr) {
                //this matches the target, either directly or desc
                if ($this->rule_json_path) {
                    $json_res = JsonPath::get($attr->attribute_value,$this->rule_json_path);
                    if (empty($json_res)) { continue;}
                    if (empty($json_res[0])) { continue;}
                }
                $total_sum+= $this->rule_value * $this->rule_weight;
            }
        });

        return $total_sum > 0;
    }


}
