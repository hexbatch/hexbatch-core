<?php

namespace App\Models;

use App\Enums\Rules\RuleDataActionType;
use App\Enums\Rules\RuleTargetActionType;
use App\Enums\Rules\RuleTriggerActionType;
use App\Enums\Rules\TypeMergeJson;
use App\Enums\Rules\TypeOfChildLogic;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


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
 *
 *
 * required goes to the to-do , and can use type ancestors and the children can listen for remotes
 *
 * for event handlers, the only called are the top level rule, one can put in attributes for the events in the children, but unless
 * its for listening to remote or stack events (which are only fired when the rules are being processed) they will not be used for event triggers
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_rule_id
 * @property int target_path_id
 * @property int trigger_path_id
 * @property int data_path_id
 * @property int rule_remote_type_id
 * @property int rule_weight
 * @property int rule_value
 * @property string ref_uuid
 * @property string rule_name
 * @property ArrayObject rule_constant_data
 * @property RuleTriggerActionType attribute_trigger_action
 * @property TypeOfChildLogic child_logic
 * @property RuleDataActionType rule_data_action
 * @property RuleTargetActionType target_action
 * @property TypeMergeJson target_writing_method
 *
 *
 * @property string created_at
 * @property string updated_at
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
        'rule_constant_data' => AsArrayObject::class,
        'attribute_trigger_action' => RuleTriggerActionType::class,
        'child_logic' => TypeOfChildLogic::class,
        'rule_data_action' => RuleDataActionType::class,
        'target_action' => RuleTargetActionType::class,
        'target_writing_method' => TypeMergeJson::class,
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


    /**
     * @throws \Exception
     */
    public static function collectRule(Collection|string $collect,Attribute $parent_attribute) : AttributeRule {
        //todo if string then see if current namespace has permission to use (in admin group of type ns)
        //
        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                $rule = (new AttributeRule())->resolveRouteBinding($collect);
            } else {
                if ($parent_attribute->isInUse()) {
                    throw new HexbatchNotPossibleException(__('msg.type_add_rules_when_not_in_use'),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_CANNOT_EDIT);
                }
                $rule = new AttributeRule();

                $rule->editRule($collect,$parent_attribute);
            }


            $rule = AttributeRule::buildAttributeRule(id:$rule->id)->first();
            DB::commit();
            return $rule;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * @throws ValidationException
     */
    public function editRule(Collection $collect,Attribute $parent_attribute) : void {
        try {
            DB::beginTransaction();

            //rule names are the only thing that in use attributes can change

            if ($collect->has('rule_name')) {
                try {
                    if ($this->rule_name = $collect->get('rule_name')) {
                        Validator::make(['rule_name' => $this->rule_name], [
                            'rule_name' => ['required', 'string', 'max:255'],
                        ])->validate();
                    }
                } catch (ValidationException $v) {
                    throw new HexbatchNotPossibleException($v->getMessage(),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::RULE_SCHEMA_ISSUE);
                }
            }
            $parent_attribute = $this->getParentAttribute(); //todo this must not be null
            if (!$parent_attribute?->isInUse()) {

                if ($collect->has('children')) {
                    AttributeRule::where('parent_rule_id', $this->id)->delete();
                    $hint_rule = $collect->get('children');
                    if (is_string($hint_rule) || $hint_rule instanceof Collection) {
                        AttributeRule::collectRule($hint_rule, $parent_attribute);
                    }
                }

            }

            if ($collect->has('target_path')) {
                $hint_path_bound = $collect->get('target_path');
                if (is_string($hint_path_bound) || $hint_path_bound instanceof Collection) {
                    $path = Path::collectPath($hint_path_bound);
                    $this->target_path_id = $path->id;
                }
            }

            if ($collect->has('trigger_path')) {
                $hint_path_bound = $collect->get('trigger_path');
                if (is_string($hint_path_bound) || $hint_path_bound instanceof Collection) {
                    $path = Path::collectPath($hint_path_bound);
                    $this->trigger_path_id = $path->id;
                }
            }

            if ($collect->has('data_path')) {
                $hint_path_bound = $collect->get('data_path');
                if (is_string($hint_path_bound) || $hint_path_bound instanceof Collection) {
                    $path = Path::collectPath($hint_path_bound);
                    $this->data_path_id = $path->id;
                }
            }

            if ($collect->has('rule_weight')) {
                $this->rule_weight = (int)$collect->get('rule_weight');
            }

            if ($collect->has('rule_value')) {
                $this->rule_value = (int)$collect->get('rule_value');
            }

            if ($collect->has('constant_data')) {
                $data = $collect->get('constant_data');
                if ($data instanceof Collection ) {
                    $this->rule_constant_data = $data->toArray();
                } else {
                    if ($data === null || $data === '') {
                        $this->rule_constant_data = null;
                    } else {
                        $this->rule_constant_data = [$data];
                    }
                }
            }


            if ($collect->has('attribute_trigger_action')) {
                $this->attribute_trigger_action = RuleTriggerActionType::tryFromInput($collect->get('attribute_trigger_action'));
            }

            if ($collect->has('child_logic')) {
                $this->child_logic = TypeOfChildLogic::tryFromInput($collect->get('child_logic'));
            }

            if ($collect->has('rule_data_action')) {
                $this->rule_data_action = RuleDataActionType::tryFromInput($collect->get('rule_data_action'));
            }

            if ($collect->has('target_action')) {
                $this->target_action = RuleTargetActionType::tryFromInput($collect->get('target_action'));
            }

            if ($collect->has('target_writing_method')) {
                $this->target_writing_method = TypeMergeJson::tryFromInput($collect->get('target_writing_method'));
            }

            $this->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getParentAttribute() : ?Attribute {

        //todo implement getting parent
        return null;
    }


}
