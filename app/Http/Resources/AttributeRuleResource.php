<?php

namespace App\Http\Resources;

use App\Models\AttributeRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 */
class AttributeRuleResource extends JsonResource
{

    protected int $n_display_level = 1;
    public function __construct($resource, int $n_display_level = 1) {
        parent::__construct($resource);
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
            return [$this->rule_target->getName()];
        }
        else if ($this->n_display_level === 1) {
            $arr =  [
                'target' => $this->rule_target->getName()
            ];
        }
        else {
            $arr =  [
                'target' =>  new AttributeResource($this->rule_target,$this->n_display_level - 1 ),
            ];
        }

        if (!$this->b_brief) {
            $arr['type'] = $this->rule_type->value;
        }
        if ($this->rule_weight !== AttributeRule::DEFAULT_WEIGHT) {
            $arr['weight'] = $this->rule_weight;
        }

        if ($this->rule_numeric_min) {
            $arr['min'] = $this->rule_numeric_min;
        }

        if ($this->rule_numeric_max) {
            $arr['max'] = $this->rule_numeric_max;
        }

        if ($this->rule_regex) {
            $arr['regex'] = $this->rule_regex;
        }
        return $arr;
    }
}
