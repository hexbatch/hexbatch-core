<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\TimeBound::time_spans()
 */
class AttributeResource extends JsonResource
{
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
        ];

        if ($request->query->getString('tz')) {
            $ret['alt'] = [
                'created_at' => Carbon::createFromTimestamp($this->created_at_ts,$request->query->getString('tz'))->toIso8601String(),
            ];
        }
        return $ret;
    }
}
