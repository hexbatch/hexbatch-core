<?php

namespace App\Helpers\Attributes;


use App\Enums\Attributes\AttributeRuleType;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;

use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\AttributeRule;
use App\Models\AttributeRuleBundle;
use App\Models\ElementType;
use App\Rules\BoundNameReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RuleGathering
{
    //todo update the rule gathering
    public ?AttributeRule $current_rule;
    public Attribute $attribute;
    public ElementType $parent_type;


    public ?int $rule_trigger_attribute_id = null;
    public ?int $target_descendant_range = null;


    public ?int $rule_weight = null;
    public ?int $rule_value = null;
    public ?string $rule_json_path = null;
    public ?AttributeRuleType $rule_type = null;
    public ?string $rule_name = null;

    public function __construct(Request $request,ElementType $parent_type,Attribute $attribute,?AttributeRule $current_rule = null )
    {
        $this->current_rule = $current_rule;
        $this->attribute = $attribute;
        $this->parent_type = $parent_type;

        if ($request->request->has('rule_name')) {
            $this->rule_name = mb_trim($request->request->getString('rule_name'));
            if(!$this->rule_name) {$this->rule_name = null;}
            $this->validateName($this->rule_name);
        }

        if ($request->request->has('rule_weight')) {
            $this->rule_weight = $request->request->getInt('rule_weight');
            if(!$this->rule_weight) {$this->rule_weight = null;}
        }

        if ($request->request->has('rule_value')) {
            $this->rule_value = $request->request->getInt('rule_value');
            if(!$this->rule_value) {$this->rule_value = null;}
        }

        if ($request->rule_json_path->has('rule_json_path')) {
            $this->rule_json_path = mb_trim($request->request->getString('rule_json_path'));
            if(!$this->rule_json_path) {$this->rule_json_path = null;}
            Utilities::testValidJsonPath($this->rule_json_path);
        }



        if ( $request->request->has('rule_type')) {
            $test_string = $request->request->getString('rule_type');
            $this->rule_type  = AttributeRuleType::tryFrom($test_string);
            if (!$this->rule_type ) {
                throw new HexbatchNotPossibleException(__("msg.rule_needs_type_found_bad",['bad_type'=>$test_string]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::RULE_SCHEMA_ISSUE);
            }
        }

        if (!$this->current_rule && !$this->rule_type) {
            throw new HexbatchNotPossibleException(__("msg.rule_needs_type"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RULE_SCHEMA_ISSUE);
        }

        if ( $request->request->has('target_attribute')) {
            $hint_attribute = $request->request->getString('target_attribute');
            /** @var Attribute $target_attribute */
            $target_attribute = (new Attribute())->resolveRouteBinding($hint_attribute);
            $this->rule_trigger_attribute_id = $target_attribute->id;
            $current_namespace = Utilities::getCurrentNamespace();
            if (!$target_attribute->type_owner->canNamespaceViewDetails($current_namespace)) {
                throw new HexbatchNotPossibleException(__("msg.rule_can_only_target_attributes_user_can_see",['ref'=>$target_attribute->getName()]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::RULE_SCHEMA_ISSUE);
            }

            //only fill this in if the attribute is in the rule
            if ( $request->request->has('target_descendant_range')) {
                $this->target_descendant_range = $request->request->getInt('target_descendant_range');
                if ($this->target_descendant_range < 0) { $this->target_descendant_range = 0;}
            }
        }





    }

    public function assign() : AttributeRule {
        $node = $this->current_rule;
        if (!$this->current_rule) {
            $node = $this->current_rule = new AttributeRule();
            if ($this->attribute->applied_rule_bundle_id) {
                $node->rule_bundle_owner_id = $this->attribute->applied_rule_bundle_id;
            } else {
                $bundle = new AttributeRuleBundle();
                $bundle->creator_attribute_id = $this->attribute->id;
                $bundle->save();
            }

        }

        if ($this->rule_name !== null ) { $node->rule_name = $this->rule_name; }

        if (!$this->attribute->isInUse()) {
            if ($this->rule_trigger_attribute_id !== null ) { $node->rule_trigger_attribute_id = $this->rule_trigger_attribute_id; }
            if ($this->target_descendant_range !== null ) { $node->target_descendant_range = $this->target_descendant_range; }
            if ($this->rule_weight !== null ) { $node->rule_weight = $this->rule_weight; }
            if ($this->rule_value !== null ) { $node->rule_value = $this->rule_value; }
            if ($this->rule_json_path !== null ) { $node->rule_json_path = $this->rule_json_path; }
            if ($this->rule_type !== null ) { $node->rule_type = $this->rule_type; }
        }

        $node->save();
        return $node;
    }

    protected function validateName(?string $name) {
        if (!$name) {return;}
        try {
            Validator::make(['rule_name' => $name], [
                'rule_name' => ['required', 'string', new BoundNameReq()],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_NAME);
        }
    }

    public static function checkRuleEditPermission(Attribute $parent_attribute,AttributeRule $da_rule) {

        if ($parent_attribute->rule_bundle?->ref_uuid !== $da_rule->rule_owner?->ref_uuid) {
            throw new HexbatchPermissionException(__("msg.rule_not_used_by_attribute",['rule'=>$da_rule->getName(),'attr'=>$parent_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_DELETE);
        }


    }

    public static function checkRuleBelongsInAttribute(Attribute $parent_attribute,AttributeRule $da_rule) {
        $exists = AttributeRule::where('rule_bundle_owner_id',$parent_attribute->rule_bundle->id)->where('id',$da_rule->id)->exists();

        if (!$exists) {
            throw new HexbatchPermissionException(__("msg.rule_not_used_by_attribute",['rule'=>$da_rule->getName(),'attr'=>$parent_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::RULE_NOT_FOUND);
        }
    }



    public static function deleteRule(Attribute $parent_attribute,AttributeRule $doomed_rule) : void {

        static::checkRuleEditPermission($parent_attribute,$doomed_rule);

        if ($parent_attribute->isInUse()) {

            throw new HexbatchPermissionException(__("msg.rule_cannot_be_deleted_if_in_use",['ref'=>$doomed_rule->getName(),'attr'=>$parent_attribute->getName()]),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::ATTRIBUTE_CANNOT_DELETE);
        }

        $doomed_rule->delete();
    }

}
