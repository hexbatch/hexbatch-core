<?php

namespace App\Http\Resources;

use App\Models\AttributeMetum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\AttributeValuePointer::getValueDisplayForResource()
 * @uses \App\Models\Attribute::getPermissionGroupsForResource()
 * @method getPermissionGroupsForResource
 */
class AttributeResource extends JsonResource
{
    protected bool $b_brief = true;
    public function __construct($resource, bool $b_brief = true) {
        parent::__construct($resource);
        $this->b_brief = $b_brief;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ret =  [
            'uuid' => $this->ref_uuid,
            'name' => $this->attribute_name,
            'is_retired' => $this->is_retired,
            'created_at' => round($this->created_at_ts),
            'bounds'=> [
                "read_bounds"=> [
                    "read_time" => $this->b_brief? ($this->read_time_bound?->getName() ) : ($this->read_time_bound ? new TimeBoundResource($this->read_time_bound) : null),
                    "read_map"=> $this->b_brief? ($this->read_map_bound?->getName() ) : ($this->read_map_bound ? new LocationBoundResource($this->read_map_bound) : null),
                    "read_shape"=> $this->b_brief? ($this->read_shape_bound?->getName() ) : ($this->read_shape_bound ? new LocationBoundResource($this->read_shape_bound) : null),
                ],
                "write_bounds"=> [
                    "write_time" => $this->b_brief? ($this->write_time_bound?->getName() ) : ($this->write_time_bound ? new TimeBoundResource($this->write_time_bound) : null),
                    "write_map" => $this->b_brief? ($this->write_map_bound?->getName() ) : ($this->write_map_bound ? new LocationBoundResource($this->write_map_bound) : null),
                    "write_shape" => $this->b_brief? ($this->write_shape_bound?->getName() ) : ($this->write_shape_bound ? new LocationBoundResource($this->write_shape_bound) : null),
                ]
            ],
            'permissions' => [
                'user_groups' => $this->getPermissionGroupsForResource(),
                'set_requirements' => [],
            ],
            'options'=> [
                'is_constant' => $this->is_constant,
                'is_static' => $this->is_static,
                'is_final' => $this->is_final,
                'is_human' => $this->is_human,
            ],
            'value'=> [
                'is_nullable' => $this->is_nullable,
                'value_type' => $this->value_type->value,
                'value_numeric_min' => $this->value_numeric_min,
                'value_numeric_max' => $this->value_numeric_max,
                'value_regex' => $this->value_regex,
                'value_default' => empty($this->attribute_pointer)? $this->value_default : $this->attribute_pointer->getValueDisplayForResource($this->b_brief),

            ]

        ];

        $meta_part = [];
        /**
         * @var AttributeMetum $meta
         */
        foreach ($this->attribute_meta_all as $meta) {
            if ($this->b_brief) {
                $meta_part[] = $meta->getName();
            } else {
                $meta_part[] = new AttributeMetaResource($meta);
            }
        }
        $ret['meta'] = $meta_part;


        if ($request->query->getString('tz')) {
            $ret['alt'] = [
                'created_at' => Carbon::createFromTimestamp($this->created_at_ts,$request->query->getString('tz'))->toIso8601String(),
            ];
        }
        return $ret;
    }
}
