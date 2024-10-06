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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;






/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owning_rule_id
 * @property int parent_rule_node_id
 * @property int rule_event_type_id
 * @property int rule_path_id
 * @property string ref_uuid
 * @property string rule_node_name
 * @property string filter_json_path
 * @property TypeOfChildLogic rule_child_logic
 * @property TypeOfChildLogic rule_logic
 * @property TypeMergeJson rule_merge_method
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property AttributeRule owning_rule
 * @property AttributeRuleNode rule_node_parent
 */
class AttributeRuleNode extends Model
{

    protected $table = 'attribute_rule_nodes';
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




    public function rule_node_parent() : BelongsTo {
        return $this->belongsTo(AttributeRuleNode::class,'parent_rule_node_id');
    }

    public function owning_rule() : BelongsTo {
        return $this->belongsTo(AttributeRule::class,'owning_rule_id');
    }



    public function getName() : string {
        if ($this->rule_node_name) {return $this->rule_node_name;}
        return $this->ref_uuid;
    }

    public static function buildAttributeRule(
        ?int $id = null
    )
    : Builder
    {

        $build = ElementType::select('attribute_rules.*')
            ->selectRaw(" extract(epoch from  attribute_rules.created_at) as created_at_ts,  extract(epoch from  attribute_rules.updated_at) as updated_at_ts")

            /** @uses AttributeRuleNode::owning_rule() */
            /** @uses AttributeRuleNode::rule_node_parent() */
            ->with('owning_rule','rule_node_parent')
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
                    $first_build = AttributeRuleNode::buildAttributeRule(id: $first_id);
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
        Collection|string $collect, ?AttributeRuleNode $rule = null, ?AttributeRuleNode $parent_rule = null, ?AttributeRule $owner_rule = null)
    : AttributeRuleNode
    {
        // one rule or a tree of rules can be passed in to be copied,  parents can be referred by uuid, or the parents can be collected
        // rules can be edited at any time because they do not change data, just future behaviors,
        // and the currently executing rules are already set up in things, so this does not affect them
        // paths cannot be edited while in use (in unprocessed thing), but they can be replaced or newly created


        try {
            DB::beginTransaction();
            if (is_string($collect) && Utilities::is_uuid($collect)) {
                return (new AttributeRuleNode())->resolveRouteBinding($collect);
            }
            else if(!$rule) {
                //see if the uuid is set, if so, then get that
                if ($collect->has('uuid')) {
                    $maybe_uuid = $collect->get('uuid');
                    if (is_string($maybe_uuid) &&  Utilities::is_uuid($maybe_uuid) ) {
                        $rule =  (new AttributeRuleNode())->resolveRouteBinding($maybe_uuid);
                    } else {

                        throw new HexbatchNotFound(
                            __('msg.rule_not_found',['ref'=>(string)$maybe_uuid]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                            RefCodes::RULE_NOT_FOUND
                        );
                    }
                } else {
                    $rule = new AttributeRuleNode();
                }
            }


            if ($owner_rule) {
                //this will throw if parent and child set, or attr already used as owner
                $rule->owning_rule_id = $owner_rule->id;
            }
            $rule->editRule($collect,$parent_rule);

            $rule = AttributeRuleNode::buildAttributeRule(id:$rule->id)->first();
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
     * this ignores the owning attribute of the rule or chain
     * @throws \Exception
     */
    public function editRule(Collection $collect,?AttributeRuleNode $parent_rule = null) : void {
        try {
            DB::beginTransaction();


            if ($collect->has('rule_node_name')) {
                try {
                    if ($this->rule_node_name = $collect->get('rule_node_name')) {
                        Validator::make(['rule_node_name' => $this->rule_node_name], [
                            'rule_node_name' => ['required', 'string', 'max:255'],
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
                    $parent_rule =  (new AttributeRuleNode())->resolveRouteBinding($maybe_uuid);
                } else {
                    throw new HexbatchNotPossibleException(
                        __('msg.parent_rule_not_found',['ref'=>$maybe_uuid]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::RULE_SCHEMA_ISSUE);
                }
            }

            if ($parent_rule) {
                //check to make sure the parent and the child are in the same chain

                if ($parent_rule->owning_rule_id !== $this->owning_rule_id ) {
                    throw new HexbatchNotPossibleException(
                        __('msg.rule_parent_child_be_same_chain',['ref'=>$this->getName(),'other'=>$parent_rule->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::RULE_SCHEMA_ISSUE);

                }
                $this->parent_rule_node_id = $parent_rule->id;
            }



            if ($collect->has('children')) {
                collect($collect->get('children'))->each(function ($hint_child, int $key) {
                    Utilities::ignoreVar($key);
                    AttributeRuleNode::collectRule(collect: $hint_child,parent_rule: $this);
                });
            }



            if ($collect->has('rule_path')) {
                $hint_path = $collect->get('rule_path');
                if (is_string($hint_path) || $hint_path instanceof Collection) {
                    $path = Path::collectPath(collect:$hint_path);
                    $this->rule_path_id = $path->id;
                }
            }

            if (is_string($collect) && Utilities::is_uuid($collect)) {
                /**
                 * @var ElementType $event_type
                 */
                $event_type = (new ElementType())->resolveRouteBinding($collect);
                //todo see if the event type is a valid event
                $this->rule_event_type_id = $event_type->id;
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
}
