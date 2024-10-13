<?php

namespace App\Http\Resources;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\Attribute::getAncestorChain(),\App\Models\Attribute::getName()
 * @method getName(bool $b_redo = false,bool $b_strip_system_prefix = true,bool $short_name = false)
 * @method getAncestorChain(int $level = 0)
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

        if ($this->n_display_level === 1) {
            return [
                'uuid' => $this->ref_uuid,
                'name' => $this->getName()
                ];
        }

        $ret =  [
            'uuid' => $this->ref_uuid,
            'name' => $this->getName(),
            'short_name' => $this->getName(short_name: true ),

            'owner' => new ElementTypeResource($this->type_owner),
            'created_at' => Carbon::createFromTimestamp($this->created_at_ts)->toIso8601String(),
            'value_json_path' => $this->value_json_path,
            'attached_event' => $this->attached_event? new ServerEventResource($this->attached_event,null,$this->n_display_level - 1) : null ,
            'options'=> [
                'is_system' => $this->is_system,
                'is_final_attribute' => $this->is_final_attribute,
                'is_seen_in_child_elements' => $this->is_seen_in_child_elements,
            ],
            'value'=> $this->original_element_value?->element_value,
            'server_access_type'=> $this->server_access_type->value


        ];

        if ($this->attribute_parent) {
            $ret['parent'] = new AttributeResource($this->attribute_parent,null,$this->n_display_level - 1 );
        }
        $ancestors = $this->getAncestorChain(1); //do not show parent, that is above
        if (count($ancestors) ) {
            $ret['ancestors'] = [];
            foreach ($ancestors as $ancestor) {
                $ret['ancestors'][] = new AttributeResource($ancestor,null,$this->n_display_level - 1 );
            }
        }


        return $ret;
    }
}
