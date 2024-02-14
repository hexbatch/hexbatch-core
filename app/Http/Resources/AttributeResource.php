<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use App\Models\Enums\Attributes\AttributeRuleType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\AttributeValuePointer::getValueDisplayForResource()
 * @uses \App\Models\Attribute::getPermissionGroupsForResource()
 * @uses \App\Models\Attribute::getRuleGroup()
 * @uses \App\Models\Attribute::getMeta()
 * @method getPermissionGroupsForResource(int $n_display)
 * @method getRuleGroup(AttributeRuleType $rule_type,int $n_display)
 * @method getMeta(int $n_display)
 * @method getName()
 * @method getValue()
 */
class AttributeResource extends JsonResource
{
    protected int $n_display_level = 1;
    public function __construct($resource, mixed $unused = null,int $n_display_level = 1) {
        parent::__construct($resource);
        Utilities::ignoreVar($unused);
        $this->n_display_level = $n_display_level;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if ($this->n_display_level <= 0) {
            return [$this->getName()];
        }


        $ret =  [
            'uuid' => $this->ref_uuid,
            'name' => $this->getName(),
            'is_retired' => $this->is_retired,
            'created_at' => round($this->created_at_ts),
            'bounds'=> [
                "read_bounds"=> [
                    "read_time" => $this->n_display_level <=1? ($this->read_time_bound?->getName() ) : ($this->read_time_bound ? new TimeBoundResource($this->read_time_bound,null,$this->n_display_level -1) : null),
                    "read_map"=> $this->n_display_level <=1? ($this->read_map_bound?->getName() ) : ($this->read_map_bound ? new LocationBoundResource($this->read_map_bound,null,$this->n_display_level -1) : null),
                    "read_shape"=> $this->n_display_level <=1? ($this->read_shape_bound?->getName() ) : ($this->read_shape_bound ? new LocationBoundResource($this->read_shape_bound,null,$this->n_display_level -1) : null),
                ],
                "write_bounds"=> [
                    "write_time" => $this->n_display_level <=1? ($this->write_time_bound?->getName() ) : ($this->write_time_bound ? new TimeBoundResource($this->write_time_bound,null,$this->n_display_level -1) : null),
                    "write_map" => $this->n_display_level <=1? ($this->write_map_bound?->getName() ) : ($this->write_map_bound ? new LocationBoundResource($this->write_map_bound,null,$this->n_display_level -1) : null),
                    "write_shape" => $this->n_display_level <=1? ($this->write_shape_bound?->getName() ) : ($this->write_shape_bound ? new LocationBoundResource($this->write_shape_bound,null,$this->n_display_level -1) : null),
                ]
            ],
            'requirements'=> [
               'elements'=> [
                   'required_siblings'=> $this->getRuleGroup(AttributeRuleType::REQUIRED,$this->n_display_level -1),
                   'forbidden_siblings'=> $this->getRuleGroup(AttributeRuleType::FORBIDDEN,$this->n_display_level -1)
               ],
                'sets'=> [
                    'allergies'=> $this->getRuleGroup(AttributeRuleType::ALLERGY,$this->n_display_level -1),
                    'affinities'=> $this->getRuleGroup(AttributeRuleType::AFFINITY,$this->n_display_level -1)
                ]
            ],
            'permissions' => [
                'user_groups' => $this->getPermissionGroupsForResource($this->n_display_level -1),
                'set_requirements' => [
                    'is_read_policy_all'=> $this->is_read_policy_all,
                    'is_write_policy_all'=> $this->is_write_policy_all,
                    'read'=> $this->getRuleGroup(AttributeRuleType::READ,$this->n_display_level -1),
                    'write'=> $this->getRuleGroup(AttributeRuleType::WRITE,$this->n_display_level -1)
                ],
            ],
            'options'=> [
                'is_final' => $this->is_final,
                'is_human' => $this->is_human,
            ],
            'value'=> [

                'type' => $this->value_type->value,
                'min' => $this->value_numeric_min,
                'max' => $this->value_numeric_max,
                'regex' => $this->value_regex,
                'default' => empty($this->attribute_pointer)? $this->getValue() : $this->attribute_pointer->getValueDisplayForResource($this->n_display_level-1),
                'is_nullable' => $this->is_nullable,
            ],
            'meta' => $this->getMeta($this->n_display_level - 1)

        ];

        if (is_null($this->value_numeric_min)) {
            unset($ret['value']['min']);
        }
        if (is_null($this->value_numeric_max)) {
            unset($ret['value']['max']);
        }
        if (empty($this->value_regex)) {
            unset($ret['value']['regex']);
        }

        return $ret;
    }
}
