<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 */
class AttributeRuleResource extends JsonResource
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
            return [$this->rule_name];
        }
        else if ($this->n_display_level === 1) {
            $arr =  [
                'name' => $this->rule_name,
                'uuid' => $this->ref_uuid,
                'target' => $this->rule_target?->getName(),
                'rule_rw_group' => $this->rule_rw_group?->getName(),
                'rule_time_bound' => $this->rule_time_bound?->getName(),
                'rule_location_bound' => $this->rule_location_bound?->getName(),

            ];
        }
        else {
           $arr = [
               'name' => $this->rule_name,
               'uuid' => $this->ref_uuid
           ];

            if($this->target) {
                $arr['target'] = new AttributeResource($this->rule_target,null,$this->n_display_level - 1 );
            }

            if($this->rule_rw_group) {
                $arr['rule_rw_group'] = new UserGroupResource($this->rule_rw_group,null,$this->n_display_level - 1);
            }

            if($this->rule_time_bound) {
                $arr['rule_time_bound'] = new TimeBoundResource($this->rule_time_bound,null,$this->n_display_level - 1);
            }

            if($this->rule_location_bound) {
                $arr['rule_location_bound'] = new LocationBoundResource($this->rule_location_bound,null,$this->n_display_level - 1);
            }
        }

        $arr['type'] = $this->rule_type->value;
        $arr['rule_value'] = $this->rule_value;
        $arr['weight'] = $this->rule_weight;
        $arr['rule_json_path'] = $this->rule_json_path;
        $arr['rule_lang'] = $this->rule_lang;


        return $arr;
    }
}
