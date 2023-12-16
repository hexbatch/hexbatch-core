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
        $ret =  [
            'uuid' => $this->ref_uuid,
            'bound_name' => $this->bound_name,
            'bound_start' => (float)$this->bound_start_ts,
            'bound_stop' => (float)$this->bound_stop_ts,
            'is_retired' => $this->is_retired,
            'bound_period_length' => $this->bound_period_length,
            'bound_cron' => $this->bound_cron,
            'spans' => new TimeBoundSpanCollection($this->time_spans)
        ];
        if ($request->query->getBoolean('skip_spans')) {
            unset($ret['spans']);
        }
        return $ret;
    }
}
