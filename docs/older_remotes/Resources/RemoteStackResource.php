<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Models\RemoteStack;

/**
 * @mixin RemoteStack
 */
class RemoteStackResource extends JsonResource
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
        $init_ret = [
            'uuid' => $this->ref_uuid,
            'owner' => $this->stack_owner->getName(),
            'parent' => $this->parent_stack?->getName(),
            "category"=>$this->remote_stack_category?->value,
            "status"=>$this->remote_stack_status?->value,
            "number_activities" => count($this->children_activities??[]),
            "number_children" => count($this->children_stacks),
        ];

        if ($this->n_display_level <=1) {
            return $init_ret;
        }

        $init_ret['starting_activity_data'] = $this->starting_activity_data;
        $init_ret['ending_data'] = $this->ending_activity_data;

        $init_ret['activities'] = [];
        foreach ($this->children_activities as $active) {
            $init_ret['activities'][] = new RemoteActivityResource($active,$this->n_display_level - 1);
        }

        $init_ret['children'] = [];
        foreach ($this->children_stacks as $stack) {
            $init_ret['children'][] = new RemoteStackResource($stack,$this->n_display_level - 1);
        }


        return $init_ret;


    }
}
