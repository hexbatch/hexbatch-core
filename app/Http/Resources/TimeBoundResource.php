<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'name' => $this->bound_name,
            'start' => (float)$this->bound_start_ts,
            'stop' => (float)$this->bound_stop_ts,
            'is_retired' => $this->is_retired,
            'period_length' => $this->bound_period_length,
            'cron' => $this->bound_cron,
            'cron_timezone' => $this->bound_cron_timezone,
            'spans' => new TimeBoundSpanCollection($this->time_spans)
        ];
        if ($request->query->getBoolean('skip_spans')) {
            unset($ret['spans']);
        }
        if ($request->query->getString('tz')) {
            $ret['alt'] = [
                'start' => Carbon::createFromTimestamp($this->bound_start_ts,$request->query->getString('tz'))->toIso8601String(),
                'stop' => Carbon::createFromTimestamp($this->bound_stop_ts,$request->query->getString('tz'))->toIso8601String(),
            ];
        }
        return $ret;
    }
}
