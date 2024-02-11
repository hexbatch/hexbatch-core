<?php

namespace App\Helpers\Attributes\Build;

use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\AttributeRule;
use App\Models\Enums\AttributeRuleType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttributeRuleGathering
{

    /**
    * @var AttributeRule[] $rules
    */
    public array $rules = [];
    public bool $is_read_policy_all = false;
    public bool $is_write_policy_all = false;

    /*
     requirements:
      elements:
          required_siblings: [attribute ids] for sharing the same  type or element
          forbidden_siblings: [attribute ids] cannot be in the same element or type
      sets:
          allergies: [force_rules] cannot be in the same set if force rules apply
          affinities: [force_rules] this can only be in the set where force rules apply
    permissions:
        set_requirements:
          is_read_policy_all: bool
          is_write_policy_all: bool
          read: [] attribute ids  : based on policy, if one, then any matches in a set makes it readable, or all must match
          write: [] attribute ids : based on policy, if one, then any matches in a set makes it readable, or all must match
     */



    public function __construct(Request $request)
    {
        $required_siblings_block = new Collection();
        $forbidden_siblings_block = new Collection();
        $allergies_block = new Collection();
        $affinities_block = new Collection();
        $set_requirements_block = new Collection();
        $read_block = new Collection();
        $write_block = new Collection();
        if ($request->request->has('requirements')) {
            $requirements_block = $request->collect('requirements');
            if ($requirements_block->has('elements')) {
                $elements_block = collect($requirements_block->get('elements'));
                if ($elements_block->has('required_siblings')) {
                    $required_siblings_block = collect($elements_block->get('required_siblings'));
                }
                if ($elements_block->has('forbidden_siblings')) {
                    $forbidden_siblings_block = collect($elements_block->get('forbidden_siblings'));
                }
            } //end elements block

            if ($requirements_block->has('sets')) {
                $sets_block = collect($requirements_block->get('sets'));
                if ($sets_block->has('allergies')) {
                    $allergies_block = collect($sets_block->get('allergies'));
                }
                if ($sets_block->has('affinities')) {
                    $affinities_block = collect($sets_block->get('affinities'));
                }
            } //end sets block
        } //end requirements block


        if ($request->request->has('permissions')) {
            $permissions_block = $request->collect('permissions');
            if ($permissions_block->has('set_requirements')) {
                $set_requirements_block = collect($permissions_block->get('set_requirements'));
                if ($set_requirements_block->has('read')) {
                    $read_block = collect($set_requirements_block->get('read'));
                }
                if ($set_requirements_block->has('write')) {
                    $write_block = collect($set_requirements_block->get('write'));
                }
            } //end elements block
        } //end permissions block

        if($set_requirements_block->has('is_read_policy_all')) {
            $this->is_read_policy_all = Utilities::boolishToBool($set_requirements_block->get('is_read_policy_all'));
        }
        if($set_requirements_block->has('is_write_policy_all')) {
            $this->is_write_policy_all = Utilities::boolishToBool($set_requirements_block->get('is_write_policy_all'));
        }

        foreach ($required_siblings_block as $sibling) {
            $this->rules[] = AttributeRule::createRule($sibling,AttributeRuleType::REQUIRED);
        }

        foreach ($forbidden_siblings_block as $sibling) {
            $this->rules[] = AttributeRule::createRule($sibling,AttributeRuleType::FORBIDDEN);
        }

        foreach ($affinities_block as $affinity) {
            $this->rules[] = AttributeRule::createRule($affinity,AttributeRuleType::AFFINITY);
        }

        foreach ($allergies_block as $allergy) {
            $this->rules[] = AttributeRule::createRule($allergy,AttributeRuleType::ALLERGY);
        }

        foreach ($read_block as $attr) {
            $this->rules[] = AttributeRule::createRule($attr,AttributeRuleType::READ);
        }

        foreach ($write_block as $attr) {
            $this->rules[] = AttributeRule::createRule($attr,AttributeRuleType::WRITE);
        }

    } //end constructor

    public function assign(Attribute $attribute) {
        try {
            DB::beginTransaction();
            /** @var AttributeRule $what */
            foreach ($this->rules as $what) {
                $what->rule_parent_attribute_id = $attribute->id;
                if ($what->delete_mode) {
                    $what->deleteModeActivate();
                } else {
                    $what->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
    }


}
