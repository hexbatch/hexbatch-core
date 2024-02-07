<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @uses \App\Models\TimeBound::time_spans()
 * @method getName()
 */
class TimeBoundResource extends JsonResource
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
        if ($this->n_display_level <=0) {
            return [$this->getName()];
        }

        $ret =  [
            'uuid' => $this->ref_uuid,
            'name' => $this->getName(),
            'start' => (float)$this->bound_start_ts,
            'stop' => (float)$this->bound_stop_ts,
            'is_retired' => $this->is_retired,
            'period_length' => $this->bound_period_length,
            'cron' => $this->bound_cron,
            'cron_timezone' => $this->bound_cron_timezone,

        ];
        if ($this->n_display_level > 1) {
            $ret['spans'] = new TimeBoundSpanCollection($this->time_spans);
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
