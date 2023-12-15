<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\TimeBound::time_spans()
 */
class TimeBoundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->ref_uuid,
            'bound_name' => $this->bound_name,
            'bound_start' => $this->bound_start_ts,
            'bound_stop' => $this->bound_stop_ts,
            'is_retired' => $this->is_retired,
            'bound_period_length' => $this->bound_period_length,
            'bound_cron' => $this->bound_cron,
            'spans' => TimeBoundSpanCollection::collection($this->time_spans),
        ];
    }
}
