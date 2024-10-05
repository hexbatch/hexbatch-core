<?php

namespace App\Models;

use App\Enums\Rules\TypeMergeJson;
use App\Enums\Rules\TypeOfChildLogic;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;





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
 * Remote chains can listen to ancestors so can be reused for different things. Ancestors run parallel and can refuse
 * (always and logic when listening to multiple event rules)
 *
 * todo rules can be edited even if the type is in use and has elements, this is because rules are static
 *
 *
 *
 * required goes to the to-do , and can use type ancestors and the children can listen for remotes
 *
 * event handlers are ignored in rule chains
 * * unless the top rule (with an attribute owning id) has an event its listening to
 * * a command is executed by a child, and the parent (not necessarily the top level one) can listen to events caused by the child command
 *
 * All rules are passed information from the api call or event
 *   caller ns (directly or indirectly)
 *   api type
 *   event type
 *   thing ref
 *   set id of the context
 *   server type
 *   thing uuid
 * This is sent via a standard element that always has the same id and uuid, but has different reads. No writes, its just a special system hook
 *
 * Short circuit,
 *  rule chain A parent of B, B returns true or false and A logic makes it true or false no matter what A does, A will not do any target stuff, and will pass the data up
 *
 * //  when attr is inherited, the parent rule is run before the child rule, if the parent fails the child rule does not run. going back to the root ancestor
    //  events have no children, so can only listen to one event at a time

remotes are executed by command in the rule

//todo group operations are rule sets, each operation step is mini api, make standard attributes each have the rules to do the group operation

rule children pass up either path results or data
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owning_attribute_id
 * @property int parent_rule_id
 * @property int rule_event_type_id
 * @property int rule_path_id
 * @property string ref_uuid
 * @property string rule_name
 * @property string filter_json_path
 * @property TypeOfChildLogic child_logic
 * @property TypeOfChildLogic my_logic
 * @property TypeMergeJson rule_merge_method
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
        'child_logic' => TypeOfChildLogic::class,
        'my_logic' => TypeOfChildLogic::class,
        'rule_merge_method' => TypeMergeJson::class,
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

            if ($collect->has('rule_path')) {
                $hint_path_bound = $collect->get('rule_path');
                if (is_string($hint_path_bound) || $hint_path_bound instanceof Collection) {
                    $path = Path::collectPath($hint_path_bound);
                    $this->rule_path_id = $path->id;
                }
            }










            if ($collect->has('child_logic')) {
                $this->child_logic = TypeOfChildLogic::tryFromInput($collect->get('child_logic'));
            }





            if ($collect->has('rule_merge_method')) {
                $this->rule_merge_method = TypeMergeJson::tryFromInput($collect->get('rule_merge_method'));
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
