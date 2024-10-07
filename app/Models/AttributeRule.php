<?php

namespace App\Models;

use App\Enums\Rules\TypeMergeJson;
use App\Enums\Rules\TypeOfChildLogic;
use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 *  rule chain A parent of B, B returns true or false and A logic makes it true or false no matter what A does, A will not do any target stuff,
 *   and will pass the data up
 *
 * //  when attr is inherited, the parent rule is run before the child rule, if the parent fails the child rule does not run. going back to the root ancestor
    //  events have no children, so can only listen to one event at a time

remotes are executed by command in the rule



rule children pass up either path results or data

the rules can react there when creation events to things in the set, the paths in the rules can filter about whose home set we want
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owning_server_event_id
 * @property int parent_rule_id
 * @property int rule_event_type_id
 * @property int rule_path_id
 * @property string ref_uuid
 * @property string rule_name
 * @property string filter_json_path
 * @property TypeOfChildLogic rule_child_logic
 * @property TypeOfChildLogic rule_logic
 * @property TypeMergeJson rule_merge_method
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property ServerEvent owning_event
 * @property AttributeRule rule_parent
 * @property AttributeRule[] rule_children
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
        'rule_child_logic' => TypeOfChildLogic::class,
        'rule_logic' => TypeOfChildLogic::class,
        'rule_merge_method' => TypeMergeJson::class,
    ];




    public function rule_parent() : BelongsTo {
        return $this->belongsTo(AttributeRule::class,'parent_rule_id');
    }

    public function rule_children() : HasMany {
        return $this->hasMany(AttributeRule::class,'parent_rule_id')
            /** @uses AttributeRule::rule_children() */
            ->with('rule_children');
    }

    public function owning_event() : BelongsTo {
        return $this->belongsTo(ServerEvent::class,'owning_server_event_id'); //todo fixup
    }



    public function getName() : string {
        if ($this->rule_name) {return $this->rule_name;}
        return $this->ref_uuid;
    }

    public static function buildAttributeRule(
        ?int $id = null
    )
    : Builder
    {

        $build = ElementType::select('attribute_rules.*')
            ->selectRaw(" extract(epoch from  attribute_rules.created_at) as created_at_ts,  extract(epoch from  attribute_rules.updated_at) as updated_at_ts")

            /** @uses AttributeRule::owning_event() */
            /** @uses AttributeRule::rule_parent(),AttributeRule::rule_children() */
            ->with('owning_event','rule_parent')
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



    public static function collectRule(
        Collection|string $collect,?AttributeRule $rule = null,?AttributeRule $parent_rule = null,?ServerEvent $owner_event = null)
    : AttributeRule
    {
        // one rule or a tree of rules can be passed in to be copied,  parents can be referred by uuid, or the parents can be collected

        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                return (new AttributeRule())->resolveRouteBinding($collect);
            }
            else if(!$rule) {
                //see if the uuid is set, if so, then get that
                if ($collect->has('uuid')) {
                    $maybe_uuid = $collect->get('uuid');
                    if (is_string($maybe_uuid) &&  Utilities::is_uuid($maybe_uuid) ) {
                        $rule =  (new AttributeRule())->resolveRouteBinding($maybe_uuid);
                    } else {

                        throw new HexbatchNotFound(
                            __('msg.rule_not_found',['ref'=>(string)$maybe_uuid]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                            RefCodes::RULE_NOT_FOUND
                        );
                    }
                } else {
                    $rule = new AttributeRule();
                }
            }


            if ($owner_event) {
                $rule->owning_server_event_id = $owner_event->id;
            }
            $rule->editRule($collect,$parent_rule);

            $rule = AttributeRule::buildAttributeRule(id:$rule->id)->first();
            DB::commit();
            return $rule;
        }

        catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof HexbatchCoreException) {
                throw $e;
            }
            throw new HexbatchNotPossibleException(
                $e->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RULE_SCHEMA_ISSUE);

        }
    }


    /**
     *
     * A rule or a rule chain can be created or edited here
     * If editing, pass in the rule uuid for lookup of the parent
     * this ignores the owning event of the rule or chain
     * @throws \Exception
     */
    public function editRule(Collection $collect,?AttributeRule $parent_rule = null) : void {
        try {
            DB::beginTransaction();


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



            if ($collect->has('parent')) {
                $maybe_uuid = $collect->get('parent');
                if (is_string($maybe_uuid) &&  Utilities::is_uuid($maybe_uuid) ) {
                    $parent_rule =  (new AttributeRule())->resolveRouteBinding($maybe_uuid);
                } else {
                    throw new HexbatchNotPossibleException(
                        __('msg.parent_rule_not_found',['ref'=>$maybe_uuid]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::RULE_SCHEMA_ISSUE);
                }
            }

            if ($parent_rule) {
                //check to make sure the parent and the child are in the same chain
                $parent_event_id = $parent_rule->owning_server_event_id;
                $this_event_id = $this->owning_server_event_id;

                if (!$parent_event_id || !$this_event_id || ($this_event_id !== $parent_event_id)) {
                    throw new HexbatchNotPossibleException(
                        __('msg.rule_parent_child_be_same_chain',['ref'=>$this->getName(),'other'=>$parent_rule->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::RULE_SCHEMA_ISSUE);

                }
                $this->parent_rule_id = $parent_rule->id;
            }



            if ($collect->has('children')) {
                $my_event = ServerEvent::buildEvent(id:$this->owning_server_event_id)->first();
                collect($collect->get('children'))->each(function ($hint_child, int $key) use($my_event) {
                    Utilities::ignoreVar($key);
                    AttributeRule::collectRule(collect: $hint_child,parent_rule: $this, owner_event: $my_event);
                });
            }



            if ($collect->has('rule_path')) {
                $hint_path = $collect->get('rule_path');
                if (is_string($hint_path) || $hint_path instanceof Collection) {
                    $path = Path::collectPath($hint_path);
                    $this->rule_path_id = $path->id;
                }
            }


            if ($collect->has('event')) {
                $hint_event = $collect->get('event');
                if (is_string($hint_event) && Utilities::is_uuid($hint_event)) {
                    /**
                     * @var ElementType $event_type
                     */
                    $event_type = (new ElementType())->resolveRouteBinding($hint_event);
                    //todo see if the event type is a valid event
                    $this->rule_event_type_id = $event_type->id;
                }
            }





            if ($collect->has('rule_child_logic')) {
                $this->rule_child_logic = TypeOfChildLogic::tryFromInput($collect->get('rule_child_logic'));
            }

            if ($collect->has('rule_logic')) {
                $this->rule_logic = TypeOfChildLogic::tryFromInput($collect->get('rule_logic'));
            }


            if ($collect->has('rule_merge_method')) {
                $this->rule_merge_method = TypeMergeJson::tryFromInput($collect->get('rule_merge_method'));
            }

            if ($collect->has('filter_json_path')) {
                $this->filter_json_path = $collect->get('filter_json_path');
                Utilities::testValidJsonPath($this->filter_json_path);
            }

            try {
                $this->save();
            } catch (\Exception $f) {
                throw new HexbatchNotPossibleException(
                    __('msg.rule_cannot_be_edited',['ref'=>$this->getName(),'error'=>$f->getMessage()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::RULE_SCHEMA_ISSUE);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    public function delete_subtree() :void {
        if ($this->owning_event->isInUse()) {
            throw new HexbatchNotFound(
                __('msg.rule_cannot_be_deleted_if_in_use',['ref'=>$this->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::RULE_NOT_FOUND
            );
        }
        try {
            DB::beginTransaction();
            $this->delete();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
